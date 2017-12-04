import hljs from 'Core/highlight.js/lib/highlight';
import php from 'Core/highlight.js/lib/languages/php';
import twig from 'Core/highlight.js/lib/languages/twig';

hljs.registerLanguage('php', php);
hljs.registerLanguage('twig', twig);

hljs.initHighlightingOnLoad();
