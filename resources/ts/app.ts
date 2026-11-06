'use strict';

((window, document) => {
    /*
     * Saved input: remember what someone typed into the join form in case
     * they navigate away before submitting.
     */
    const initForms = () => {
        document.querySelectorAll<HTMLElement>('[data-save]').forEach((form) => {
            initFields(form);
        });
    };

    const initFields = (form: HTMLElement) => {
        const formName = form.dataset.save as string;

        form.querySelectorAll<HTMLInputElement | HTMLTextAreaElement>('input[type=text],input[type=email],textarea').forEach((input) => {
            const inputName = input.getAttribute('name') as string;

            input.addEventListener('input', () => {
                saveInput(formName, inputName, input.value);
            });

            if (input.value === '') {
                setTimeout(() => {
                    input.value = loadInput(formName, inputName) ?? '';
                }, 5);
            }
        });
    };

    const saveInput = (formName: string, inputName: string, value: string) => {
        window.localStorage.setItem(`saved-input.${formName}.${inputName}`, value);
    };

    const loadInput = (formName: string, inputName: string) => {
        return window.localStorage.getItem(`saved-input.${formName}.${inputName}`);
    };

    const clearInput = (formName: string | undefined) => {
        if (formName === undefined || formName === '') {
            console.error('Unknown form name');
        }

        for (const key of Object.keys(window.localStorage)) {
            if (key.startsWith('saved-input.' + formName)) {
                window.localStorage.removeItem(key);
            }
        }
    };

    /*
     * Toast: a small message at the bottom of the screen.
     */
    let toastTimer: number | undefined;

    const toast = (message: string) => {
        const element = document.getElementById('toast');

        if (!element) {
            return;
        }

        element.textContent = message;
        element.classList.remove('opacity-0', 'translate-y-4');

        window.clearTimeout(toastTimer);
        toastTimer = window.setTimeout(() => {
            element.classList.add('opacity-0', 'translate-y-4');
        }, 2200);
    };

    /*
     * Copy buttons: <button data-copy="#input-id">. Uses the native share
     * sheet on phones when a share title is given, the clipboard otherwise.
     */
    const initCopyButtons = () => {
        document.querySelectorAll<HTMLButtonElement>('[data-copy]').forEach((button) => {
            button.addEventListener('click', async () => {
                const source = document.querySelector<HTMLInputElement>(button.dataset.copy as string);
                const value = source?.value ?? '';
                const shareTitle = button.dataset.shareTitle;

                if (shareTitle && navigator.share && window.matchMedia('(pointer: coarse)').matches) {
                    try {
                        await navigator.share({ title: shareTitle, url: value });

                        return;
                    } catch {
                        // Cancelled or unsupported: fall back to copying.
                    }
                }

                try {
                    await navigator.clipboard.writeText(value);
                } catch {
                    source?.select();
                    document.execCommand('copy');
                }

                toast('Link copied to clipboard');
            });
        });
    };

    /*
     * Questionnaire: live "x of n answered" count, and a warning before
     * leaving the page with unsaved answers.
     */
    const initQuestionnaire = () => {
        const form = document.querySelector<HTMLFormElement>('[data-questionnaire]');

        if (!form) {
            return;
        }

        const questions = Array.from(form.querySelectorAll<HTMLElement>('[data-question]'));
        const counter = document.querySelector<HTMLElement>('[data-answered-count]');
        const progress = document.querySelector<HTMLElement>('[data-progress]');
        let dirty = false;

        const isAnswered = (question: HTMLElement) => {
            const textarea = question.querySelector('textarea');

            if (textarea) {
                return textarea.value.trim() !== '';
            }

            const checked = question.querySelector<HTMLInputElement>('input[type=radio]:checked');

            return checked !== null && checked.value !== '';
        };

        const update = () => {
            const answered = questions.filter(isAnswered).length;

            if (counter) {
                counter.textContent = String(answered);
            }

            if (progress && questions.length > 0) {
                progress.style.width = `${Math.round((answered / questions.length) * 100)}%`;
            }
        };

        form.addEventListener('input', () => {
            dirty = true;
            update();
        });

        form.addEventListener('submit', () => {
            dirty = false;
        });

        window.addEventListener('beforeunload', (event) => {
            if (dirty) {
                event.preventDefault();
            }
        });
    };

    initForms();
    initCopyButtons();
    initQuestionnaire();

    (window as any).clearSavedInput = clearInput;
})(window, document);
