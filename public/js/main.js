document.addEventListener('DOMContentLoaded', function() {
    // Smooth scrolling for navigation links
    document.querySelectorAll('header nav a').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            document.querySelector(targetId).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    // AJAX for contact form
    const contactForm = document.getElementById('contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);
            const responseDiv = document.getElementById('contact-form-response');

            fetch('contact_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                responseDiv.innerHTML = `<p class="${data.status}">${data.message}</p>`;
                if (data.status === 'success') {
                    form.reset();
                }
            })
            .catch(error => {
                responseDiv.innerHTML = `<p class="error">An error occurred. Please try again.</p>`;
                console.error('Error:', error);
            });
        });
    }

    // Display download error message if present
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('download_error')) {
        const docId = urlParams.get('doc_id');
        const downloadForm = document.querySelector(`form[action="download_handler.php"] input[value="${docId}"]`);
        if(downloadForm) {
            const errorP = document.createElement('p');
            errorP.style.color = 'red';
            errorP.textContent = 'Incorrect password. Please try again.';
            downloadForm.parentElement.insertAdjacentElement('afterend', errorP);
        }
    }
});