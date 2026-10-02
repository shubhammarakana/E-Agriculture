document.addEventListener('DOMContentLoaded', function () {
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('leaf_image');
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const removeBtn = document.getElementById('removeImg');
    const form = document.getElementById('diseaseForm');
    const errorMsg = document.getElementById('errorMsg');

    const resultPanel = document.getElementById('resultPanel');
    const loading = document.getElementById('loading');
    const resultContent = document.getElementById('resultContent');

    // Drag and Drop
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.style.borderColor = 'var(--primary)';
        uploadArea.style.background = 'rgba(255,255,255,0.8)';
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.style.borderColor = '#ccc';
        uploadArea.style.background = 'transparent';
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.style.borderColor = '#ccc';
        uploadArea.style.background = 'transparent';

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFile(files[0]);
        }
    });

    // Click to Upload
    uploadArea.addEventListener('click', () => fileInput.click());

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
            handleFile(fileInput.files[0]);
        }
    });

    // Handle File Validation and Preview
    function handleFile(file) {
        errorMsg.style.display = 'none';

        // Validate Type
        const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!validTypes.includes(file.type)) {
            showError('Invalid file type. Please upload a JPG or PNG image.');
            return;
        }

        // Validate Size (10MB)
        if (file.size > 10 * 1024 * 1024) {
            showError('File is too large. Max size is 10MB.');
            return;
        }

        // Show Preview
        const reader = new FileReader();
        reader.onload = (e) => {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
            uploadArea.style.display = 'none';
        }
        reader.readAsDataURL(file);

        // Update Input (if dropped)
        if (fileInput.files[0] !== file) {
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput.files = dataTransfer.files;
        }
    }

    function showError(msg) {
        errorMsg.textContent = msg;
        errorMsg.style.display = 'block';
    }

    // Remove Image
    removeBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        fileInput.value = '';
        preview.style.display = 'none';
        uploadArea.style.display = 'block';
        resultPanel.style.display = 'none';
    });

    // Form Submission
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        if (!fileInput.files.length) {
            showError('Please upload an image first.');
            return;
        }

        // UI Updates
        resultPanel.style.display = 'block';
        loading.style.display = 'block';
        resultContent.style.display = 'none';
        resultPanel.style.borderLeftColor = 'transparent';

        // Prepare Data
        const formData = new FormData(form);

        // AJAX Request
        fetch('process_disease.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                loading.style.display = 'none';
                resultContent.style.display = 'block';

                if (data.success) {
                    renderResult(data.data);
                } else {
                    alert('Error: ' + data.message);
                    resultPanel.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                loading.style.display = 'none';
                alert('An error occurred during analysis.');
            });
    });

    function renderResult(data) {
        document.getElementById('diseaseName').textContent = data.disease;
        document.getElementById('confidenceBadge').textContent = data.confidence + '% Confidence';
        document.getElementById('symptomsText').textContent = data.symptoms;
        document.getElementById('organicTreatment').textContent = data.treatment.organic;
        document.getElementById('chemicalTreatment').textContent = data.treatment.chemical;

        const tipsList = document.getElementById('preventiveTips');
        tipsList.innerHTML = '';
        data.preventive_tips.forEach(tip => {
            const li = document.createElement('li');
            li.textContent = tip;
            tipsList.appendChild(li);
        });

        // Color Coding
        if (data.severity === 'Healthy') {
            resultPanel.style.borderLeftColor = '#22c55e';
            document.getElementById('confidenceBadge').style.background = '#22c55e';
        } else if (data.severity === 'Critical') {
            resultPanel.style.borderLeftColor = '#ef4444';
            document.getElementById('confidenceBadge').style.background = '#ef4444';
        } else {
            resultPanel.style.borderLeftColor = '#f59e0b';
            document.getElementById('confidenceBadge').style.background = '#f59e0b';
        }
    }
});
