import './bootstrap';
import Chart from 'chart.js/auto';
import { marked } from 'marked';
import Prism from 'prismjs';
import 'prismjs/components/prism-php';
import 'prismjs/components/prism-javascript';
import 'prismjs/components/prism-json';

window.Chart = Chart;
window.marked = marked;
window.Prism = Prism;

marked.setOptions({
    breaks: true,
    gfm: true,
});

window.renderAiMarkdown = () => {
    document.querySelectorAll('[data-markdown]').forEach((element) => {
        if (element.dataset.rendered === 'true') {
            return;
        }

        element.innerHTML = marked.parse(element.textContent || '');
        element.dataset.rendered = 'true';
    });

    Prism.highlightAll();
};

window.copyAiResponse = async (id) => {
    const element = document.getElementById(id);

    if (! element) {
        return;
    }

    await navigator.clipboard.writeText(element.textContent || '');
};

window.addEventListener('DOMContentLoaded', window.renderAiMarkdown);
