<!DOCTYPE HTML>

<html>
<head>
    @if(isset($seoTagsWithValues) && count($seoTagsWithValues))
        @foreach($seoTagsWithValues as $seoTagsWithValue)
            {!! $seoTagsWithValue !!}
        @endforeach
    @endif
    <link rel="shortcut icon" href="./favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="{{ asset('assets/css/main.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Open+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Google reCAPTCHA with explicit rendering -->
    <script src="https://www.google.com/recaptcha/api.js?onload=onRecaptchaLoad&render=explicit" async defer></script>

        @foreach($headScripts as $script)
            @if($script->type === 'external')
                <script src="{{ $script->content }}"></script>
            @else
                {!! $script->content !!}
            @endif
        @endforeach
</head>
<body>
    @foreach($bodyTopScripts as $script)
        @if($script->type === 'external')
            <script src="{{ $script->content }}"></script>
        @else
            {!! $script->content !!}
        @endif
    @endforeach


    @if(isset($homePage) && $homePage->contents && $homePage->contents->count())
        @foreach($homePage->contents as $content)
            {!! $content->content !!}
        @endforeach
    @endif

<a href="tel:+373766688" class="phone-button" aria-label="Call phone number">
    <i class="fa fa-phone" aria-hidden="true"></i>
</a>

<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

<script src="{{ asset('assets/js/script.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // CSRF token injected from Laravel
        const csrfToken = '{{ csrf_token() }}';

        // Replace with your actual reCAPTCHA site key
        const recaptchaSiteKey = '{{ env('RECAPTCHA_SITE_KEY') }}';

        // Debug: Check if site key is loaded
        if (!recaptchaSiteKey || recaptchaSiteKey.trim() === '') {
            console.error('reCAPTCHA Site Key is missing! Check your Laravel config and .env file.');
            return;
        }

        // Languages data from backend
        const allLanguages = @json($allLanguages);
        const currentLanguage = '{{ $currentLanguage }}';

        // Function to populate language select
        function populateLanguageSelect() {
            const languageSelect = document.getElementById('language');

            if (languageSelect && allLanguages && allLanguages.length > 0) {
                // Clear existing options
                languageSelect.innerHTML = '';

                // Add options dynamically
                allLanguages.forEach(language => {
                    const option = document.createElement('option');
                    option.value = language.code;
                    option.textContent = language.name;

                    // Set as selected if it matches current language
                    if (language.code === currentLanguage) {
                        option.selected = true;
                    }

                    languageSelect.appendChild(option);
                });
            }
        }

        // Populate language select on page load
        populateLanguageSelect();

        // Handle language change
        const languageSelect = document.getElementById('language');
        if (languageSelect) {
            languageSelect.addEventListener('change', function() {
                const selectedLanguage = this.value;
                if (selectedLanguage) {
                    // Get the base domain (without language prefix)
                    const currentUrl = window.location.href;
                    const baseUrl = window.location.origin;

                    // Redirect to the new language URL
                    window.location.href = `${baseUrl}/${selectedLanguage}`;
                }
            });
        }

        // Function to add reCAPTCHA to forms
        function addRecaptchaToForms() {
            const formsWithRecaptcha = document.querySelectorAll('.form-to-send.with-recaptcha');

            formsWithRecaptcha.forEach((form, index) => {
                // Create a unique ID for each reCAPTCHA container
                const recaptchaId = `recaptcha-${index}`;

                // Create reCAPTCHA container
                const recaptchaContainer = document.createElement('div');
                recaptchaContainer.id = recaptchaId;
                recaptchaContainer.className = 'g-recaptcha';
                recaptchaContainer.style.marginBottom = '15px';

                // Find the submit button - prioritize button[type="submit"] then input[type="submit"]
                const submitButton = form.querySelector('button[type="submit"]') || form.querySelector('input[type="submit"]');

                // Always insert before the submit button if it exists
                if (submitButton) {
                    form.insertBefore(recaptchaContainer, submitButton);
                } else {
                    // If no submit button found, log warning and append to end
                    console.warn('No submit button found in form with reCAPTCHA. Adding reCAPTCHA to end of form.');
                    form.appendChild(recaptchaContainer);
                }

                // Store the reCAPTCHA widget ID on the form for later use
                form.dataset.recaptchaId = recaptchaId;
            });
        }

        // Function to render reCAPTCHA widgets
        function renderRecaptcha() {
            if (typeof grecaptcha !== 'undefined' && grecaptcha.render) {
                const formsWithRecaptcha = document.querySelectorAll('.form-to-send.with-recaptcha');

                formsWithRecaptcha.forEach((form) => {
                    const recaptchaId = form.dataset.recaptchaId;
                    const container = document.getElementById(recaptchaId);

                    if (container && !container.dataset.rendered) {
                        try {
                            const widgetId = grecaptcha.render(container, {
                                'sitekey': recaptchaSiteKey,
                                'theme': 'light',
                                'size': 'normal'
                            });
                            container.dataset.widgetId = widgetId;
                            container.dataset.rendered = 'true';
                        } catch (error) {
                            console.error('Error rendering reCAPTCHA:', error);
                        }
                    }
                });
            }
        }

        // Add reCAPTCHA containers to forms
        addRecaptchaToForms();

        // Global callback function for reCAPTCHA
        window.onRecaptchaLoad = function() {
            renderRecaptcha();
        };

        // Fallback: try to render after a short delay if callback doesn't work
        setTimeout(function() {
            if (typeof grecaptcha !== 'undefined') {
                renderRecaptcha();
            }
        }, 2000);

        // Select all forms with the class 'form-to-send'
        const forms = document.querySelectorAll('.form-to-send');

        forms.forEach(form => {
            form.addEventListener('submit', async function(event) {
                event.preventDefault();

                // Check if this form has reCAPTCHA
                const hasRecaptcha = form.classList.contains('with-recaptcha');

                if (hasRecaptcha) {
                    // Get reCAPTCHA response
                    const recaptchaContainer = form.querySelector('.g-recaptcha');
                    let recaptchaResponse = '';

                    if (recaptchaContainer && recaptchaContainer.dataset.widgetId) {
                        try {
                            recaptchaResponse = grecaptcha.getResponse(parseInt(recaptchaContainer.dataset.widgetId));
                        } catch (error) {
                            console.error('Error getting reCAPTCHA response:', error);
                        }
                    }

                    // Validate reCAPTCHA
                    if (!recaptchaResponse || recaptchaResponse.length === 0) {
                        alert('Vă rugăm să completați reCAPTCHA pentru a continua.');
                        return;
                    }
                }

                // Collect all form data dynamically
                const formData = new FormData(form);
                const data = {};
                formData.forEach((value, key) => {
                    data[key] = value;
                });

                // Add the current URL to the data object
                data.currentUrl = window.location.href;

                // Add reCAPTCHA response if present
                if (hasRecaptcha) {
                    const recaptchaContainer = form.querySelector('.g-recaptcha');
                    if (recaptchaContainer && recaptchaContainer.dataset.widgetId) {
                        try {
                            data.recaptcha_response = grecaptcha.getResponse(parseInt(recaptchaContainer.dataset.widgetId));
                        } catch (error) {
                            console.error('Error getting reCAPTCHA response:', error);
                        }
                    }
                }

                try {
                    const response = await fetch('/post-form', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify(data)
                    });

                    const result = await response.json();

                    if (response.ok) {
                        // Success handling
                        alert('Mesajul a fost trimis cu succes!');
                        form.reset(); // Clear the specific form

                        // Reset reCAPTCHA if present
                        if (hasRecaptcha) {
                            const recaptchaContainer = form.querySelector('.g-recaptcha');
                            if (recaptchaContainer && recaptchaContainer.dataset.widgetId) {
                                try {
                                    grecaptcha.reset(parseInt(recaptchaContainer.dataset.widgetId));
                                } catch (error) {
                                    console.error('Error resetting reCAPTCHA:', error);
                                }
                            }
                        }
                    } else {
                        // Error handling
                        alert(result.message || 'A apărut o eroare la trimiterea mesajului.');

                        // Reset reCAPTCHA on error if present
                        if (hasRecaptcha) {
                            const recaptchaContainer = form.querySelector('.g-recaptcha');
                            if (recaptchaContainer && recaptchaContainer.dataset.widgetId) {
                                try {
                                    grecaptcha.reset(parseInt(recaptchaContainer.dataset.widgetId));
                                } catch (error) {
                                    console.error('Error resetting reCAPTCHA:', error);
                                }
                            }
                        }
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('A apărut o eroare la trimiterea mesajului.');

                    // Reset reCAPTCHA on error if present
                    if (hasRecaptcha) {
                        const recaptchaContainer = form.querySelector('.g-recaptcha');
                        if (recaptchaContainer && recaptchaContainer.dataset.widgetId) {
                            try {
                                grecaptcha.reset(parseInt(recaptchaContainer.dataset.widgetId));
                            } catch (error) {
                                console.error('Error resetting reCAPTCHA:', error);
                            }
                        }
                    }
                }
            });
        });
    });

    @foreach($bodyBottomScripts as $script)
        @if($script->type === 'external')
            <script src="{{ $script->content }}"></script>
            @else
                {!! $script->content !!}
        @endif
    @endforeach

</script>
</body>
</html>
