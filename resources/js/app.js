import './bootstrap';

document.querySelectorAll('[data-car-make-select]').forEach((makeSelect) => {
    const form = makeSelect.closest('form') ?? document;
    const modelSelect = form.querySelector('[data-car-model-select]');

    if (!modelSelect) {
        return;
    }

    const syncModels = () => {
        const selectedMake = makeSelect.value;
        let selectedModelIsVisible = false;

        modelSelect.querySelectorAll('option').forEach((option) => {
            const optionMake = option.dataset.make;
            const isPlaceholder = !option.value;
            const isVisible = isPlaceholder || !selectedMake || optionMake === selectedMake;

            option.hidden = !isVisible;
            option.disabled = !isVisible;

            if (option.selected && isVisible) {
                selectedModelIsVisible = true;
            }
        });

        modelSelect.querySelectorAll('optgroup').forEach((group) => {
            const hasVisibleOption = Array.from(group.querySelectorAll('option')).some((option) => !option.hidden);

            group.hidden = selectedMake ? !hasVisibleOption : false;
            group.disabled = selectedMake ? !hasVisibleOption : false;
        });

        if (!selectedModelIsVisible) {
            modelSelect.value = '';
        }
    };

    makeSelect.addEventListener('change', syncModels);
    syncModels();
});

document.querySelectorAll('[data-collapsible-panel]').forEach((panel) => {
    const toggle = panel.querySelector('[data-collapsible-toggle]');
    const content = panel.querySelector('[data-collapsible-content]');

    if (!toggle || !content) {
        return;
    }

    const updateLabel = () => {
        toggle.textContent = content.classList.contains('hidden') ? 'Expand' : 'Minimize';
    };

    toggle.addEventListener('click', () => {
        content.classList.toggle('hidden');
        updateLabel();
    });

    updateLabel();
});

document.querySelectorAll('[data-search-mode-option]').forEach((option) => {
    const form = option.closest('form');

    if (!form) {
        return;
    }

    const prompt = form.querySelector('[data-search-prompt]');
    const input = form.querySelector('[data-search-input]');
    const options = form.querySelectorAll('[data-search-mode-option]');

    const syncSearchMode = () => {
        options.forEach((item) => {
            const radio = item.querySelector('input[type="radio"]');
            const isActive = radio?.checked;

            item.classList.toggle('bg-yellow-400', isActive);
            item.classList.toggle('text-zinc-950', isActive);
            item.classList.toggle('font-extrabold', isActive);
            item.classList.toggle('bg-zinc-200', !isActive);
            item.classList.toggle('text-zinc-800', !isActive);
            item.classList.toggle('font-bold', !isActive);

            if (isActive) {
                if (prompt) {
                    prompt.textContent = item.dataset.prompt || '';
                }

                if (input) {
                    input.placeholder = item.dataset.placeholder || '';
                }
            }
        });
    };

    option.addEventListener('click', syncSearchMode);
    option.querySelector('input[type="radio"]')?.addEventListener('change', syncSearchMode);
    syncSearchMode();
});

document.querySelectorAll('[data-search-input]').forEach((input) => {
    const wrapper = input.closest('.relative');
    const panel = wrapper?.querySelector('[data-search-suggestion-panel]');
    const toggle = wrapper?.querySelector('[data-search-suggestion-toggle]');
    const suggestions = JSON.parse(input.dataset.searchSuggestions || '[]');

    if (!panel || suggestions.length === 0) {
        return;
    }

    const hideSuggestions = () => {
        panel.classList.add('hidden');
    };

    const showSuggestions = () => {
        const term = input.value.trim().toLowerCase();
        const matches = suggestions
            .filter((suggestion) => !term || suggestion.toLowerCase().includes(term))
            .slice(0, 8);

        if (matches.length === 0) {
            panel.replaceChildren();

            const empty = document.createElement('div');
            empty.className = 'px-4 py-3 text-zinc-500';
            empty.textContent = 'No suggestions found';
            panel.appendChild(empty);
        } else {
            panel.replaceChildren(...matches.map((suggestion) => {
                const button = document.createElement('button');

                button.type = 'button';
                button.className = 'block w-full px-4 py-3 text-left font-semibold text-zinc-700 hover:bg-yellow-100';
                button.dataset.searchSuggestion = suggestion;
                button.textContent = suggestion;

                return button;
            }));
        }

        panel.classList.remove('hidden');
    };

    input.addEventListener('focus', showSuggestions);
    input.addEventListener('input', showSuggestions);
    toggle?.addEventListener('click', showSuggestions);

    panel.addEventListener('click', (event) => {
        const button = event.target.closest('[data-search-suggestion]');

        if (!button) {
            return;
        }

        input.value = button.dataset.searchSuggestion;
        hideSuggestions();
        input.focus();
    });

    document.addEventListener('click', (event) => {
        if (!wrapper?.contains(event.target)) {
            hideSuggestions();
        }
    });
});

document.querySelectorAll('[data-review-modal-open]').forEach((toggle) => {
    const card = toggle.closest('article');
    const modal = card?.querySelector('[data-review-modal]');
    const closeButtons = modal?.querySelectorAll('[data-review-modal-close]') ?? [];

    if (!modal) {
        return;
    }

    const openModal = () => {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    };

    const closeModal = () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    };

    toggle.addEventListener('click', openModal);

    closeButtons.forEach((button) => {
        button.addEventListener('click', closeModal);
    });

    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
});

document.querySelectorAll('[data-similar-slider]').forEach((slider) => {
    const track = slider.querySelector('[data-similar-track]');
    const previous = slider.querySelector('[data-similar-prev]');
    const next = slider.querySelector('[data-similar-next]');

    if (!track || !previous || !next) {
        return;
    }

    const scrollByCard = (direction) => {
        const firstCard = track.firstElementChild;
        const gap = 20;
        const distance = firstCard ? firstCard.getBoundingClientRect().width + gap : track.clientWidth;

        track.scrollBy({
            left: direction * distance,
            behavior: 'smooth',
        });
    };

    previous.addEventListener('click', () => scrollByCard(-1));
    next.addEventListener('click', () => scrollByCard(1));
});
