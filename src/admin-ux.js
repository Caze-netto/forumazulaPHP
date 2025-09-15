document.addEventListener('DOMContentLoaded', () => {
    const conteudoInput = document.getElementById('conteudo');
    const preview = document.getElementById('preview-content');
    const toolbar = document.querySelector('.editor-toolbar');

    if (!conteudoInput || !preview || !toolbar) return;

    // Função para inserir Markdown no textarea
    function insertMarkdown(markdown) {
        const start = conteudoInput.selectionStart;
        const end = conteudoInput.selectionEnd;
        conteudoInput.setRangeText(markdown, start, end, 'select');
        conteudoInput.focus();
        conteudoInput.dispatchEvent(new Event('input'));
    }

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

        const renderPreview = () => {
            preview.innerHTML = marked.parse(conteudoInput.value);
        };

        conteudoInput.addEventListener('input', renderPreview);
        renderPreview();
    }

    // Toolbar
    toolbar.addEventListener('click', (e) => {
        const button = e.target.closest('.toolbar-btn');
        if (!button) return;

        const action = button.dataset.action;
        const selectedText = conteudoInput.value.substring(conteudoInput.selectionStart, conteudoInput.selectionEnd);
        let markdown = '';

        switch(action) {
            case 'heading': markdown = `## ${selectedText || 'Título'}`; break;
            case 'bold': markdown = `**${selectedText || 'texto em negrito'}**`; break;
            case 'italic': markdown = `*${selectedText || 'texto em itálico'}*`; break;
            case 'quote': markdown = selectedText
                ? selectedText.split('\n').map(line => `> ${line}`).join('\n')
                : '> Citação\n'; break;
            case 'code': markdown = `\n\`\`\`\n${selectedText || 'código'}\n\`\`\`\n`; break;
            case 'link': 
                const url = prompt('Introduza o URL do link:');
                if (url) markdown = `[${selectedText || 'texto do link'}](${url})`;
                break;
        }

        if (markdown) insertMarkdown(markdown);
    });
});