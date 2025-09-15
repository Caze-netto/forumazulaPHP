const conteudo = document.getElementById('conteudo');
const preview = document.getElementById('preview-content');

function atualizarPreview() {
    preview.innerHTML = marked.parse(conteudo.value);
    preview.querySelectorAll('pre code').forEach((block) => {
        hljs.highlightElement(block);
    });
}
conteudo.addEventListener('input', atualizarPreview);
atualizarPreview();

document.querySelectorAll('.toolbar-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const action = btn.dataset.action;
        const start = conteudo.selectionStart;
        const end = conteudo.selectionEnd;
        const text = conteudo.value.substring(start, end);
        let insert = '';

        switch(action) {
            case 'heading': insert = `## ${text || 'Título'}`; break;
            case 'bold': insert = `**${text || 'negrito'}**`; break;
            case 'italic': insert = `*${text || 'itálico'}*`; break;
            case 'quote': insert = `> ${text || 'citação'}`; break;
            case 'code': insert = `\`\`\`\n${text || 'código'}\n\`\`\``; break;
            case 'link': insert = `[${text || 'texto'}](url)`; break;
        }

        conteudo.setRangeText(insert, start, end, 'end');
        conteudo.focus();
        atualizarPreview();
    });
});