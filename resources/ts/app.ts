'use strict';

((window, document) => {
    const initForms = () => {
        document.querySelectorAll('[data-save]').forEach((form) => {
            initFields(form as HTMLElement);
        });
    };

    const initFields = (form: HTMLElement) => {
        form.querySelectorAll('input[type=text],input[type=email],textarea').forEach((input: HTMLInputElement|HTMLTextAreaElement) => {
            input.addEventListener('keyup', (event) => {
                saveInput(form.dataset.save, input.getAttribute('name'), input.value);
            });

            if (input.value === '') {
                setTimeout(() => {
                    console.log(`Loading saved input for ${form.dataset.save}.${input.getAttribute('name')}`);

                    input.value = loadInput(form.dataset.save, input.getAttribute('name'));
                }, 5);
            }
        });
    };

    const saveInput = (formName: string, inputName: string, value: string) => {
        window.localStorage.setItem(`saved-input.${formName}.${inputName}`, value);
    }

    const loadInput = (formName: string, inputName: string) => {
        return window.localStorage.getItem(`saved-input.${formName}.${inputName}`);
    }

    const clearInput = (formName: string|undefined) => {
        if (formName === undefined || formName === '') {
            console.error('Unknown form name');
        }

        for (const key of Object.keys(window.localStorage)) {
            if (key.startsWith('saved-input.' + formName)) {
                window.localStorage.removeItem(key);
            }
        }
    }

    initForms();

    (window as any).clearSavedInput = clearInput;
})(window, document);
