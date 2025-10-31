document.addEventListener('DOMContentLoaded', () => {
    const startCameraBtn = document.getElementById('startCameraBtn');
    const captureBtn = document.getElementById('captureBtn');
    const uploadPhotoBtn = document.getElementById('uploadPhotoBtn');
    const cameraContainer = document.getElementById('cameraContainer');
    const video = document.getElementById('video');
    const photoPreview = document.getElementById('photoPreview');
    const fileInput = document.getElementById('fileInput');
    const photoDataInput = document.getElementById('photo_data');

    let stream;

    // 🔴 Iniciar câmera
    startCameraBtn.addEventListener('click', async () => {
        startCamera();
    });

    function startCamera() {
        if (navigator.mediaDevices && typeof navigator.mediaDevices.getUserMedia === 'function') {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function(stream) {
                    video.srcObject = stream;
                    cameraContainer.classList.remove('hidden');
                    photoPreview.classList.add('hidden'); // esconde preview antigo
                    captureBtn.classList.remove('hidden'); // mostra botão capturar
                    startCameraBtn.classList.add('hidden'); // esconde botão iniciar
                })
                .catch(function(err) {
                    alert('Erro ao acessar a câmera: ' + err.message);
                });
        } else {
            alert('Acesso à câmera não suportado neste navegador ou contexto.');
        }
    }

    // 🔴 Capturar imagem
    captureBtn.addEventListener('click', () => {
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        const dataUrl = canvas.toDataURL('image/png');

        // Atualiza preview
        photoPreview.src = dataUrl;
        photoPreview.classList.remove('hidden');
        cameraContainer.classList.add('hidden');
        captureBtn.classList.add('hidden');
        startCameraBtn.classList.remove('hidden');

        // Salvar imagem no input hidden
        photoDataInput.value = dataUrl;

        // Parar vídeo
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
    });

    // 🔴 Upload manual de arquivo
    uploadPhotoBtn.addEventListener('click', () => {
        fileInput.click();
    });

    fileInput.addEventListener('change', () => {
        const file = fileInput.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = (e) => {
            const dataUrl = e.target.result;
            photoPreview.src = dataUrl;
            photoPreview.classList.remove('hidden');
            cameraContainer.classList.add('hidden');
            captureBtn.classList.add('hidden');
            startCameraBtn.classList.remove('hidden');

            // Salvar imagem no input hidden
            photoDataInput.value = dataUrl;
        };
        reader.readAsDataURL(file);
    });

    // 🔴 Garantir que, ao submeter, photo_data esteja preenchido
    const form = photoPreview.closest('form');
    if (form) {
        form.addEventListener('submit', () => {
            if (!photoDataInput.value) {
                console.log('Nenhuma foto selecionada');
            }
        });
    }
});
