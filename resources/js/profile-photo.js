document.addEventListener('DOMContentLoaded', () => {
    const video = document.getElementById('video');
    const captureBtn = document.getElementById('captureBtn');
    const startCameraBtn = document.getElementById('startCameraBtn');
    const uploadPhotoBtn = document.getElementById('uploadPhotoBtn');
    const fileInput = document.getElementById('fileInput');
    const photoPreview = document.getElementById('photoPreview');
    const photoDataInput = document.getElementById('photo_data');
    const cameraContainer = document.getElementById('cameraContainer');

    const canvas = document.createElement('canvas');
    let stream = null;

    /** Iniciar câmera */
    async function startCamera() {
        try {
            if (stream) stopCamera();

            cameraContainer.classList.remove('hidden');
            startCameraBtn.classList.add('hidden');
            captureBtn.classList.remove('hidden');
            photoPreview.classList.add('hidden');

            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user', width: { ideal: 240 }, height: { ideal: 240 } },
                audio: false
            });
            video.srcObject = stream;
            video.play();
        } catch (err) {
            console.error("Erro ao acessar a câmera:", err);
            alert("Não foi possível acessar a câmera. Verifique as permissões.");
        }
    }

    /** Parar câmera */
    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        video.srcObject = null;
    }

    /** Capturar foto da câmera */
    function capturePhoto() {
        if (!stream) return;
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

        const dataURL = canvas.toDataURL('image/png');
        photoPreview.src = dataURL;
        photoPreview.classList.remove('hidden');
        photoDataInput.value = dataURL;

        stopCamera();
        cameraContainer.classList.add('hidden');
        captureBtn.classList.add('hidden');
        startCameraBtn.classList.add('hidden'); // pode esconder ou mostrar "Change Photo"
    }

    /** Selecionar arquivo e redimensionar */
    function handleFileSelect(event) {
        const file = event.target.files[0];
        if (!file) return;
        if (!file.type.startsWith('image/')) return alert('Selecione uma imagem válida.');

        const reader = new FileReader();
        reader.onload = e => {
            const img = new Image();
            img.onload = () => {
                const maxSize = 800;
                let { width, height } = img;

                if (width > height && width > maxSize) {
                    height = Math.floor(height * (maxSize / width));
                    width = maxSize;
                } else if (height > maxSize) {
                    width = Math.floor(width * (maxSize / height));
                    height = maxSize;
                }

                canvas.width = width;
                canvas.height = height;
                canvas.getContext('2d').drawImage(img, 0, 0, width, height);

                const dataURL = canvas.toDataURL('image/jpeg', 0.9);
                photoPreview.src = dataURL;
                photoPreview.classList.remove('hidden');
                photoDataInput.value = dataURL;

                cameraContainer.classList.add('hidden');
                captureBtn.classList.add('hidden');
                startCameraBtn.classList.remove('hidden'); // Mostrar botão de mudar foto novamente
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    /** Event listeners */
    startCameraBtn?.addEventListener('click', startCamera);
    captureBtn?.addEventListener('click', capturePhoto);
    uploadPhotoBtn?.addEventListener('click', () => fileInput.click());
    fileInput?.addEventListener('change', handleFileSelect);
    window.addEventListener('beforeunload', stopCamera);

    /** Estado inicial */
    (() => {
        const hasPhoto = photoPreview.src && !photoPreview.src.includes('default.png');
        if (hasPhoto) {
            startCameraBtn.classList.remove('hidden');
            photoPreview.classList.remove('hidden');
        } else {
            startCameraBtn.classList.remove('hidden');
        }
        captureBtn.classList.add('hidden');
        cameraContainer.classList.add('hidden');
    })();
});
