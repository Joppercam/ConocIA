// public/js/admin.js

document.addEventListener('DOMContentLoaded', function() {
    // Toggle sidebar
    const sidebarToggle = document.getElementById('sidebarCollapse');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('content').classList.toggle('active');
        });
    }
    
    // Initialize Summernote WYSIWYG editor
    if (window.jQuery && typeof $.fn.summernote !== 'undefined') {
        $('.summernote').summernote({
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            callbacks: {
                onImageUpload: function(files) {
                    // This would typically upload to your server
                    // For now, just show a message
                    alert('Para subir imágenes, utiliza el gestor de medios.');
                }
            }
        });
    }
    
    // Initialize Select2 if available
    if (window.jQuery && typeof $.fn.select2 !== 'undefined') {
        $('.select2').select2({
            theme: 'bootstrap-5'
        });
    }
    
    // Initialize Datepicker if available
    if (window.jQuery && typeof $.fn.datepicker !== 'undefined') {
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });
    }
    
    // Initialize Clipboard.js for copy buttons
    if (typeof ClipboardJS !== 'undefined') {
        new ClipboardJS('.btn-copy');
    }
    
    // Confirm deletes
    const deleteButtons = document.querySelectorAll('.btn-delete, [data-confirm]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const message = this.dataset.confirm || '¿Estás seguro de que deseas eliminar este elemento? Esta acción no se puede deshacer.';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
    
    // Toggle publish status
    const statusSwitches = document.querySelectorAll('.status-switch');
    statusSwitches.forEach(switchEl => {
        switchEl.addEventListener('change', function() {
            const form = this.closest('form');
            form.submit();
        });
    });
    
    // Image preview before upload
    const imageInputs = document.querySelectorAll('.image-upload');
    imageInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const preview = document.getElementById(this.dataset.preview);
            if (preview && this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
    
    // File upload zone
    const dropZones = document.querySelectorAll('.file-upload-zone');
    dropZones.forEach(zone => {
        const input = document.getElementById(zone.dataset.input);
        
        // Prevent default drag behaviors
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            zone.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        // Highlight drop zone when item is dragged over
        ['dragenter', 'dragover'].forEach(eventName => {
            zone.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            zone.addEventListener(eventName, unhighlight, false);
        });
        
        function highlight() {
            zone.classList.add('active');
        }
        
        function unhighlight() {
            zone.classList.remove('active');
        }
        
        // Handle dropped files
        zone.addEventListener('drop', handleDrop, false);
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (input) {
                input.files = files;
                
                // Trigger change event
                const event = new Event('change', { bubbles: true });
                input.dispatchEvent(event);
            }
        }
        
        // Open file select dialog when zone is clicked
        zone.addEventListener('click', function() {
            if (input) {
                input.click();
            }
        });
    });
    
    // Slug generator
    const titleInputs = document.querySelectorAll('[data-slug-source]');
    titleInputs.forEach(input => {
        const slugField = document.getElementById(input.dataset.slugSource);
        if (slugField) {
            input.addEventListener('keyup', function() {
                // Only auto-generate slug if it's empty or hasn't been manually edited
                if (!slugField.dataset.manuallyEdited || slugField.dataset.manuallyEdited === 'false') {
                    slugField.value = generateSlug(this.value);
                }
            });
            
            // Mark slug as manually edited when user changes it
            slugField.addEventListener('input', function() {
                this.dataset.manuallyEdited = 'true';
            });
        }
    });
    
    function generateSlug(text) {
        return text
            .toString()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '') // Remove accents
            .toLowerCase()
            .trim()
            .replace(/\s+/g, '-') // Replace spaces with -
            .replace(/[^\w\-]+/g, '') // Remove all non-word chars
            .replace(/\-\-+/g, '-') // Replace multiple - with single -
            .replace(/^-+/, '') // Trim - from start of text
            .replace(/-+$/, ''); // Trim - from end of text
    }
    
    // Notifications badge updater
    function updateNotificationsBadge() {
        const badge = document.querySelector('#notificationsDropdown .badge');
        if (badge) {
            // This would typically be an AJAX call to get the count
            // For now, we'll just simulate it
            setTimeout(() => {
                const currentCount = parseInt(badge.textContent);
                if (!isNaN(currentCount) && currentCount > 0) {
                    badge.textContent = currentCount;
                } else {
                    badge.style.display = 'none';
                }
            }, 30000); // Check every 30 seconds
        }
    }
    
    // Call initially and set interval
    updateNotificationsBadge();
    setInterval(updateNotificationsBadge, 30000);
    
    // Tags input
    const tagInputs = document.querySelectorAll('.tags-input');
    tagInputs.forEach(container => {
        const input = container.querySelector('input[type="text"]');
        const hiddenInput = container.querySelector('input[type="hidden"]');
        const tagsList = container.querySelector('.tags-list');
        
        if (input && hiddenInput && tagsList) {
            // Initialize from hidden input
            let tags = hiddenInput.value ? hiddenInput.value.split(',') : [];
            
            // Render initial tags
            renderTags();
            
            // Add tag when pressing Enter
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ',') {
                    e.preventDefault();
                    addTag();
                }
            });
            
            // Add tag when input loses focus
            input.addEventListener('blur', addTag);
            
            function addTag() {
                const tag = input.value.trim();
                if (tag && !tags.includes(tag)) {
                    tags.push(tag);
                    updateHiddenInput();
                    renderTags();
                }
                input.value = '';
            }
            
            function removeTag(index) {
                tags.splice(index, 1);
                updateHiddenInput();
                renderTags();
            }
            
            function updateHiddenInput() {
                hiddenInput.value = tags.join(',');
            }
            
            function renderTags() {
                tagsList.innerHTML = '';
                tags.forEach((tag, index) => {
                    const tagEl = document.createElement('span');
                    tagEl.className = 'badge bg-primary me-2 mb-2';
                    tagEl.textContent = tag;
                    
                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'btn-close btn-close-white ms-2';
                    removeBtn.setAttribute('aria-label', 'Eliminar');
                    removeBtn.addEventListener('click', () => removeTag(index));
                    
                    tagEl.appendChild(removeBtn);
                    tagsList.appendChild(tagEl);
                });
            }
        }
    });
    
    // Data tables initialization
    if (window.jQuery && typeof $.fn.DataTable !== 'undefined') {
        $('.datatable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
            },
            responsive: true
        });
    }
    
    // Chart.js initializations (if any charts exist on the page)
    if (typeof Chart !== 'undefined' && document.getElementById('contentStats')) {
        // Content Statistics Chart already defined in the view
    }
});
