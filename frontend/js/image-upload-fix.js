// Sistema corrigido de upload de múltiplas imagens
class MultipleImageUpload {
    constructor() {
        this.selectedFiles = [];
        this.maxFiles = 10;
        this.maxFileSize = 5 * 1024 * 1024; // 5MB
        this.allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        
        this.initializeElements();
        this.bindEvents();
    }
    
    initializeElements() {
        this.fileInput = document.getElementById('imageUpload');
        this.uploadBtn = document.getElementById('imageUploadBtn');
        this.previewContainer = document.getElementById('imagePreviewContainer');
        this.previewGrid = document.getElementById('previewGrid');
        this.imageCount = document.getElementById('imageCount');
        this.removeAllBtn = document.getElementById('removeAllImagesBtn');
        this.form = this.fileInput?.closest('form');
    }
    
    bindEvents() {
        if (this.uploadBtn) {
            this.uploadBtn.addEventListener('click', () => this.fileInput?.click());
        }
        
        if (this.fileInput) {
            this.fileInput.addEventListener('change', (e) => this.handleFileSelection(e));
        }
        
        if (this.removeAllBtn) {
            this.removeAllBtn.addEventListener('click', () => this.removeAllImages());
        }
        
        if (this.form) {
            this.form.addEventListener('submit', (e) => this.handleFormSubmit(e));
        }
    }
    
    handleFileSelection(event) {
        const files = Array.from(event.target.files || []);
        
        // Verificar limite de arquivos
        if (this.selectedFiles.length + files.length > this.maxFiles) {
            this.showToast(`Máximo de ${this.maxFiles} imagens permitidas`);
            event.target.value = ''; // Reset input
            return;
        }
        
        // Processar cada arquivo
        files.forEach((file, index) => {
            if (this.validateFile(file)) {
                this.addFileToSelection(file, index);
            }
        });
        
        // Reset input para permitir seleção do mesmo arquivo novamente
        event.target.value = '';
        
        this.updatePreview();
    }
    
    validateFile(file) {
        // Verificar tipo
        if (!this.allowedTypes.includes(file.type)) {
            this.showToast(`Tipo de arquivo não suportado: ${file.type}`);
            return false;
        }
        
        // Verificar tamanho
        if (file.size > this.maxFileSize) {
            this.showToast(`Arquivo muito grande: ${file.name}. Máximo 5MB.`);
            return false;
        }
        
        return true;
    }
    
    addFileToSelection(file, originalIndex) {
        const fileId = `${Date.now()}_${originalIndex}_${Math.random()}`;
        const fileData = {
            id: fileId,
            file: file,
            originalIndex: originalIndex
        };
        
        this.selectedFiles.push(fileData);
    }
    
    removeImage(fileId) {
        this.selectedFiles = this.selectedFiles.filter(f => f.id !== fileId);
        this.updatePreview();
    }
    
    removeAllImages() {
        this.selectedFiles = [];
        this.updatePreview();
    }
    
    updatePreview() {
        if (!this.previewContainer || !this.previewGrid || !this.imageCount) return;
        
        if (this.selectedFiles.length === 0) {
            this.previewContainer.style.display = 'none';
            return;
        }
        
        this.previewContainer.style.display = 'block';
        this.previewGrid.innerHTML = '';
        
        // Criar preview para cada imagem
        this.selectedFiles.forEach((fileData) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const imageWrapper = document.createElement('div');
                imageWrapper.className = 'preview-image-wrapper';
                imageWrapper.innerHTML = `
                    <img src="${e.target.result}" alt="Preview" class="preview-image">
                    <button type="button" class="remove-image-btn" data-file-id="${fileData.id}">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                
                // Adicionar evento de remoção
                const removeBtn = imageWrapper.querySelector('.remove-image-btn');
                removeBtn.addEventListener('click', () => this.removeImage(fileData.id));
                
                this.previewGrid.appendChild(imageWrapper);
            };
            reader.readAsDataURL(fileData.file);
        });
        
        this.imageCount.textContent = `${this.selectedFiles.length}/${this.maxFiles} imagens`;
    }
    
    handleFormSubmit(event) {
        if (this.selectedFiles.length === 0) return; // Deixar o PHP validar se precisa de imagens
        
        // Criar novo FormData para garantir que todas as imagens sejam enviadas
        const form = event.target;
        const formData = new FormData();
        
        // Adicionar todos os campos do formulário exceto imagens
        for (let [key, value] of new FormData(form).entries()) {
            if (key !== 'imagem[]' && !key.startsWith('imagem[')) {
                formData.append(key, value);
            }
        }
        
        // Adicionar imagens uma por uma com índices específicos
        this.selectedFiles.forEach((fileData, index) => {
            formData.append(`imagem[${index}]`, fileData.file);
        });
        
        // Substituir o envio padrão
        event.preventDefault();
        
        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok) {
                window.location.reload();
            } else {
                throw new Error('Erro no servidor');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            this.showToast('Erro ao criar publicação');
        });
    }
    
    showToast(message) {
        // Implementar sistema de toast se existir
        if (window.showToast) {
            window.showToast(message);
        } else {
            alert(message);
        }
    }
}

// Inicializar quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', function() {
    new MultipleImageUpload();
});