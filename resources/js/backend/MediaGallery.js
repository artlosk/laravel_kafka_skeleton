export class MediaGallery {
    constructor(container) {
        this.container = container;
        this.name = container.dataset.name;
        
        if (!this.name) {
            throw new Error('MediaGallery: container must have data-name attribute');
        }

        this.selectors = {
            modal: `#${this.name}-mediaLibraryModal`,
            content: `#${this.name}-mediaItemsList`,
            openButton: `[data-bs-target="#${this.name}-mediaLibraryModal"]`,
            attachButton: `#${this.name}-attachSelectedMedia`,
            preview: `#${this.name}-selectedMediaPreview`,
            selectedIds: `#${this.name}-selectedMediaIds`,
            mediaOrder: `#${this.name}-mediaOrder`
        };

        this.elements = {
            modal: document.querySelector(this.selectors.modal),
            content: document.querySelector(this.selectors.content),
            openButton: document.querySelector(this.selectors.openButton),
            attachButton: document.querySelector(this.selectors.attachButton),
            preview: document.querySelector(this.selectors.preview),
            selectedIds: document.querySelector(this.selectors.selectedIds),
            mediaOrder: document.querySelector(this.selectors.mediaOrder)
        };

        if (!this.elements.modal || !this.elements.openButton) {
            throw new Error('MediaGallery: required elements not found');
        }

        this.modal = null;

        this.selectedMediaIds = new Set();
        this.isLoading = false;
        this.sortableInstance = null;
        this.previewSortableInstance = null;
        this._updatingFromAttach = false;

        this.init();
    }

    init() {
        if (window.bootstrap && window.bootstrap.Modal) {
            this.modal = new window.bootstrap.Modal(this.elements.modal, {
                backdrop: true,
                keyboard: true,
                focus: true
            });
        } else {
            throw new Error('Bootstrap Modal is not available');
        }

        this.loadInitialMedia();
        this.setupEventListeners();
    }

    loadInitialMedia() {
        const idsValue = this.elements.selectedIds?.value;
        if (idsValue) {
            const ids = idsValue.split(',')
                .map(id => id.trim())
                .filter(id => id);

            ids.forEach(id => {
                const numId = parseInt(id);
                if (!isNaN(numId) && numId > 0) {
                    this.selectedMediaIds.add(numId);
                } else if (id.startsWith('filepond-tmp/')) {
                }
            });

            const numericIds = ids.filter(id => /^\d+$/.test(id));
            if (numericIds.length > 0) {
                this.updatePreview();
            }
        }
    }

    setupEventListeners() {
        this.elements.openButton.addEventListener('click', (e) => {
            e.preventDefault();
            this.openModal();
        });
        this.elements.modal.addEventListener('hidden.bs.modal', () => {
            this.cleanup();
            this._updatingFromAttach = false;
        });
        this.elements.attachButton?.addEventListener('click', () => {
            this.attachSelected();
        });

        this.elements.preview.addEventListener('click', (e) => {
            const removeBtn = e.target.closest('.remove-media-btn');
            if (removeBtn) {
                e.preventDefault();
                e.stopPropagation();
                const mediaId = parseInt(removeBtn.dataset.mediaId);
                if (!isNaN(mediaId)) {
                    this.selectedMediaIds.delete(mediaId);
                    this.updatePreview().then(() => {
                        this.syncFormFields();
                    }).catch(() => {
                        this.syncFormFields();
                    });
                }
            }
        });

        this.elements.content.addEventListener('click', (e) => {
            if (e.target.closest('button') || e.target.closest('a')) {
                return;
            }
            
            const mediaItem = e.target.closest('.media-item') || e.target.closest('[data-media-id]');
            if (mediaItem) {
                mediaItem.classList.toggle('selected');
                const mediaId = parseInt(mediaItem.dataset.mediaId);
                if (mediaItem.classList.contains('selected')) {
                    this.selectedMediaIds.add(mediaId);
                } else {
                    this.selectedMediaIds.delete(mediaId);
                }
            }
        });

        this.elements.content.addEventListener('click', (e) => {
            const deleteBtn = e.target.closest('.delete-media-btn');
            if (deleteBtn) {
                e.preventDefault();
                e.stopPropagation();
                const mediaItem = deleteBtn.closest('.media-item') || deleteBtn.closest('[data-media-id]');
                if (mediaItem) {
                    const mediaId = parseInt(mediaItem.dataset.mediaId);
                    this.deleteMedia(mediaId, mediaItem);
                }
            }
        });

        this.elements.content.addEventListener('click', (e) => {
            const paginationLink = e.target.closest('.pagination a');
            if (paginationLink) {
                e.preventDefault();
                const url = paginationLink.getAttribute('href');
                if (url) {
                    this.loadContent(url);
                }
            }
        });
    }

    async openModal() {
        if (this.isLoading) return;

        this.isLoading = true;
        this.elements.content.innerHTML = '<div class="text-center p-4"><div class="spinner-border text-primary" role="status"></div></div>';

        try {
            this.modal.show();
            await this.loadContent(window.appConfig.routes.mediaIndex);
        } catch (error) {
            console.error('Error opening modal:', error);
            this.elements.content.innerHTML = '<div class="alert alert-danger">Ошибка загрузки галереи</div>';
        } finally {
            this.isLoading = false;
        }
    }

    async loadContent(url) {
        try {
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html',
                    'X-AdminLTE-Skip-Preloader': 'true'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const html = await response.text();

            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const mediaGallery = doc.querySelector('#media-gallery');
            
            if (mediaGallery) {
                this.elements.content.innerHTML = `<div class="media-items-container row">${mediaGallery.innerHTML}</div>`;
                const pagination = doc.querySelector('.pagination');
                if (pagination) {
                    this.elements.content.insertAdjacentHTML('beforeend', pagination.outerHTML);
                }
            } else {
                this.elements.content.innerHTML = html;
            }

            this.restoreSelectedItems();
            this.initSortable();
        } catch (error) {
            console.error('Error loading content:', error);
            this.elements.content.innerHTML = '<div class="alert alert-danger">Ошибка загрузки контента</div>';
        }
    }

    restoreSelectedItems() {
        this.elements.content.querySelectorAll('.media-item').forEach(item => {
            const mediaId = parseInt(item.dataset.mediaId);
            if (this.selectedMediaIds.has(mediaId)) {
                item.classList.add('selected');
            }
        });
    }

    initSortable() {
        const sortableContainer = this.elements.content.querySelector('.media-items-container');
        if (!sortableContainer || !window.Sortable) return;

        if (this.sortableInstance) {
            this.sortableInstance.destroy();
        }

        this.sortableInstance = new window.Sortable(sortableContainer, {
            animation: 150,
            handle: '.card',
            draggable: '.col-md-2',
            onEnd: () => {

            }
        });
    }

    async deleteMedia(mediaId, element) {
        if (!confirm('Вы уверены, что хотите удалить этот файл?')) {
            return;
        }

        try {
            const response = await fetch(`/admin/media/${mediaId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'X-AdminLTE-Skip-Preloader': 'true'
                }
            });

            const data = await response.json();

            if (response.ok && data.message) {
                element.remove();
                this.selectedMediaIds.delete(mediaId);
                this.updatePreview().then(() => {
                    this.syncFormFields();
                }).catch(() => {
                    this.syncFormFields();
                });
                
                if (window.toastr) {
                    window.toastr.success(data.message);
                }
            } else {
                throw new Error(data.error || 'Ошибка удаления');
            }
        } catch (error) {
            console.error('Error deleting media:', error);
            if (window.toastr) {
                window.toastr.error('Ошибка при удалении файла');
            }
        }
    }

    attachSelected() {
        const selectedInModal = [];
        this.elements.content.querySelectorAll('.media-item.selected').forEach(item => {
            const mediaId = parseInt(item.dataset.mediaId);
            if (!isNaN(mediaId) && mediaId > 0) {
                selectedInModal.push(mediaId);
                this.selectedMediaIds.add(mediaId);
            }
        });
        
        const selectedItems = Array.from(this.selectedMediaIds);
        
        if (selectedItems.length === 0 && selectedInModal.length === 0) {
            if (window.toastr) {
                window.toastr.warning('Выберите хотя бы один файл');
            }
            return;
        }

        if (this.elements.attachButton && document.activeElement === this.elements.attachButton) {
            this.elements.attachButton.blur();
        }

        setTimeout(() => {
            this._updatingFromAttach = true;
            this.updatePreview().then(() => {
                this.syncFormFields();
                this._updatingFromAttach = false;
                setTimeout(() => {
                    this.modal.hide();
                }, 100);
            }).catch(error => {
                console.error('Error updating preview:', error);
                this.syncFormFields();
                this._updatingFromAttach = false;
                setTimeout(() => {
                    this.modal.hide();
                }, 100);
            });
        }, 50);
    }

    async updatePreview() {
        const ids = Array.from(this.selectedMediaIds);
        const filepondItems = Array.from(this.elements.preview.querySelectorAll('.media-preview-item')).map(el => ({
            element: el,
            mediaId: el.dataset.mediaId
        }));
        
        if (ids.length === 0 && filepondItems.length === 0) {
            this.elements.preview.innerHTML = '';
            return Promise.resolve();
        }

        try {
            const numericIds = ids.filter(id => {
                const idStr = String(id);
                return typeof id === 'number' || /^\d+$/.test(idStr);
            });
            
            let mediaItems = [];
            if (numericIds.length > 0) {
                const response = await fetch(`${window.appConfig.routes.mediaGetByIds}?ids=${numericIds.join(',')}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-AdminLTE-Skip-Preloader': 'true'
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                mediaItems = await response.json();
            }

            const galleryHtml = mediaItems.map(media => {
                const thumbUrl = media.url_thumb || media.url;
                const isImage = media.mime_type && media.mime_type.startsWith('image/');

                return `
                    <div class="col-md-2 mb-3" data-media-id="${media.id}">
                        <div class="card h-100">
                            <div class="card-body p-2 text-center">
                                ${isImage 
                                    ? `<img src="${thumbUrl}" class="img-fluid rounded" alt="${media.name}" style="max-height: 100px; object-fit: cover;">`
                                    : `<i class="fas fa-file fa-3x text-muted"></i>`
                                }
                            </div>
                            <div class="card-footer p-1 text-center">
                                <small class="d-block text-truncate" title="${media.file_name}">${media.file_name}</small>
                                <button type="button" class="btn btn-sm btn-danger mt-1 remove-media-btn" data-media-id="${media.id}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            this.elements.preview.innerHTML = galleryHtml;

            filepondItems.forEach(({ element, mediaId }) => {
                if (String(mediaId).startsWith('filepond-tmp/')) {
                    this.elements.preview.appendChild(element);
                }
            });

            if (!this._updatingFromAttach) {
                this.syncFormFields();
            }

            this.initPreviewSortable();

        } catch (error) {
            console.error('Error updating preview:', error);
            this.elements.preview.innerHTML = '<div class="alert alert-danger">Ошибка загрузки превью</div>';
            throw error;
        }
    }

    initPreviewSortable() {
        if (!window.Sortable) return;

        if (this.previewSortableInstance) {
            this.previewSortableInstance.destroy();
        }

        this.previewSortableInstance = new window.Sortable(this.elements.preview, {
            animation: 150,
            handle: '.card',
            onEnd: (evt) => {
                const items = Array.from(this.elements.preview.children);
                const orderedIds = [];
                
                items.forEach(item => {
                    let mediaId = item.dataset?.mediaId;
                    if (!mediaId) {
                        const nested = item.querySelector('[data-media-id]');
                        if (nested) {
                            mediaId = nested.dataset.mediaId;
                        }
                    }
                    if (mediaId) {
                        orderedIds.push(String(mediaId));
                    }
                });

                if (this.elements.mediaOrder) {
                    this.elements.mediaOrder.value = orderedIds.join(',');
                }
                if (this.elements.selectedIds) {
                    this.elements.selectedIds.value = orderedIds.join(',');
                }
            }
        });
    }

    cleanup() {
        if (this.sortableInstance) {
            this.sortableInstance.destroy();
            this.sortableInstance = null;
        }
    }

    syncFormFields() {
        const allIds = [];
        const allElements = Array.from(this.elements.preview.children);
        
        allElements.forEach(el => {
            let mediaId = el.dataset?.mediaId;

            if (!mediaId) {
                const nestedElement = el.querySelector('[data-media-id]');
                if (nestedElement) {
                    mediaId = nestedElement.dataset.mediaId;
                }
            }
            
            if (mediaId && !allIds.includes(mediaId)) {
                allIds.push(mediaId);
            }
        });

        Array.from(this.selectedMediaIds).forEach(id => {
            const idStr = String(id);
            if (!allIds.includes(idStr)) {
                allIds.push(idStr);
            }
        });

        if (this.elements.selectedIds) {
            this.elements.selectedIds.value = allIds.join(',');
        }
        if (this.elements.mediaOrder) {
            this.elements.mediaOrder.value = allIds.join(',');
        }
        
    }

    destroy() {
        this.cleanup();
        if (this.previewSortableInstance) {
            this.previewSortableInstance.destroy();
            this.previewSortableInstance = null;
        }
        if (this.modal) {
            this.modal.dispose();
        }
    }
}
