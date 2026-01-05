@extends('layouts.admin')

@php
    use Illuminate\Support\Str;
@endphp

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">
            <i class="fas fa-images mr-2"></i>
            Медиафайлы
        </h1>
        <div class="d-flex">
            @can('upload-media')
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#uploadModal">
                    <i class="fas fa-upload"></i> Загрузить файлы
                </button>
            @endcan
        </div>
    </div>
@endsection

@section('admin_content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list mr-2"></i>
                Библиотека медиафайлов
            </h3>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row" id="media-gallery">
                @forelse($mediaItems as $media)
                    <div class="col-md-2 mb-4 media-item" data-media-id="{{ $media->id }}">
                        <div class="card h-100">
                            <div class="card-body p-2 text-center">
                                @if($media->mime_type && Str::startsWith($media->mime_type, 'image/'))
                                    <img
                                        src="{{ $media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : $media->getUrl() }}"
                                        class="img-fluid rounded"
                                        alt="{{ $media->name }}"
                                        style="max-height: 120px; object-fit: cover;">
                                @else
                                    <i class="fas fa-file fa-4x text-muted"></i>
                                @endif
                            </div>
                            <div class="card-footer p-1 text-center">
                                <small class="d-block text-truncate" title="{{ $media->file_name }}">
                                    {{ $media->file_name }}
                                </small>
                                <div class="d-flex gap-1 justify-content-center mt-1">
                                    <button type="button" class="btn btn-outline-info btn-sm"
                                            onclick="copyToClipboard('{{ $media->getUrl() }}')"
                                            title="Копировать URL">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                    @can('delete-media')
                                        <button type="button" class="btn btn-outline-danger btn-sm delete-media-btn"
                                                data-media-id="{{ $media->id }}"
                                                title="Удалить">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-images fa-3x mb-3"></i>
                            <h5>Медиафайлы не найдены</h5>
                            <p>Загрузите файлы, чтобы начать работу с медиабиблиотекой</p>
                        </div>
                    </div>
                @endforelse
            </div>

            @if($mediaItems->hasPages())
                <div class="mt-3">
                    {{ $mediaItems->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Upload Modal -->
    @can('upload-media')
        <div class="modal fade" id="uploadModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-upload mr-2"></i>
                            Загрузка файлов
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="uploadForm" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="mediaFiles" class="form-label" form-label class="form-label">Выберите
                                    файлы</label>
                                <input type="file" class="form-control" id="mediaFiles" name="files[]" multiple
                                       accept="image/*,video/*,audio/*,.pdf,.doc,.docx">
                            </div>
                            <div class="progress" style="display: none;">
                                <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="button" class="btn btn-primary" onclick="uploadFiles()">
                            <i class="fas fa-upload"></i> Загрузить
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endcan

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function () {
                // Show success feedback
                const button = event.target.closest('button');
                const originalHTML = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check"></i>';
                button.classList.add('btn-success');
                button.classList.remove('btn-outline-info');

                setTimeout(() => {
                    button.innerHTML = originalHTML;
                    button.classList.remove('btn-success');
                    button.classList.add('btn-outline-info');
                }, 2000);
            });
        }

        function uploadFiles() {
            const formData = new FormData(document.getElementById('uploadForm'));
            const progressBar = document.querySelector('.progress-bar');
            const progressContainer = document.querySelector('.progress');

            progressContainer.style.display = 'block';

            fetch('{{ route("backend.media.upload") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Ошибка загрузки: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Произошла ошибка при загрузке файлов');
                });
        }

        // Delete media functionality
        document.addEventListener('click', function (e) {
            if (e.target.closest('.delete-media-btn')) {
                const mediaId = e.target.closest('.delete-media-btn').dataset.mediaId;
                if (confirm('Вы уверены, что хотите удалить этот файл?')) {
                    fetch(`{{ url('/admin/media') }}/${mediaId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                document.querySelector(`[data-media-id="${mediaId}"]`).remove();
                            } else {
                                alert('Ошибка удаления: ' + data.message);
                            }
                        });
                }
            }
        });
    </script>
@endsection
