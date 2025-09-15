document.addEventListener('DOMContentLoaded', () => {
    const conteudoInput = document.getElementById('conteudo');
    const preview = document.getElementById('preview-content');
    const toolbar = document.querySelector('.editor-toolbar');

    if (!conteudoInput || !preview || !toolbar) return;

    // Configuração do Marked.js
    if (typeof marked !== 'undefined') {
        marked.setOptions({
            breaks: true,
            gfm: true,
            highlight: (code, lang) => {
                const language = hljs.getLanguage(lang) ? lang : 'plaintext';
                return hljs.highlight(code, { language }).value;
            }
        });
        conteudoInput.addEventListener('input', () => {
            preview.innerHTML = marked.parse(conteudoInput.value);
        });
        preview.innerHTML = marked.parse(conteudoInput.value);
    }

    // Barra de ferramentas
    toolbar.addEventListener('click', (e) => {
        const button = e.target.closest('.toolbar-btn');
        if (!button) return;

        const action = button.dataset.action;
        const textarea = conteudoInput;
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const selectedText = textarea.value.substring(start, end);
        let markdown = '';

        switch(action) {
            case 'heading': markdown = `## ${selectedText || 'Título'}`; break;
            case 'bold': markdown = `**${selectedText || 'texto em negrito'}**`; break;
            case 'italic': markdown = `*${selectedText || 'texto em itálico'}*`; break;
            case 'quote': markdown = `> ${selectedText || 'Citação'}\n`; break;
            case 'code': markdown = '\n```\n' + (selectedText || 'código') + '\n```\n'; break;
            case 'link':
                const url = prompt('Introduza o URL do link:');
                if (url) markdown = `[${selectedText || 'texto do link'}](${url})`;
                break;
        }

        if (markdown) {
            textarea.setRangeText(markdown, start, end, 'select');
            textarea.focus();
            textarea.dispatchEvent(new Event('input'));
        }
    });
});
