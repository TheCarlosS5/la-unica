"""Evita que una limpieza documental quite las capturas esenciales de las guías."""
from html.parser import HTMLParser
from pathlib import Path
import json

ROOT = Path(__file__).resolve().parents[1]
WIKI = ROOT / 'wiki'
manifest = json.loads((WIKI / 'img/actuales/capturas.json').read_text(encoding='utf8'))
expected = {item['archivo'] for item in manifest['capturas']}
assert len(expected) == len(manifest['capturas']) and expected, 'Manifiesto vacío o con archivos duplicados'


class Article(HTMLParser):
    def __init__(self):
        super().__init__()
        self.images = []
        self.figures = 0
        self.captions = 0

    def handle_starttag(self, tag, attrs):
        attrs = dict(attrs)
        if tag == 'img':
            assert attrs.get('alt', '').strip(), 'Imagen sin descripción alternativa'
            assert int(attrs.get('width', '0')) > 0 and int(attrs.get('height', '0')) > 0, 'Imagen sin dimensiones'
            self.images.append(attrs.get('src', ''))
        if tag == 'figure':
            assert 'captura' in attrs.get('class', '').split(), 'Figura sin visor de capturas'
            self.figures += 1
        if tag == 'figcaption':
            self.captions += 1


minimum = {'guia/clientes': 7, 'guia/productos': 7, 'guia/facturas': 9,
           'guia/reportes': 7, 'guia/sobre-nosotros': 6, 'guia/movil': 3,
           'guia/introduccion': 1, 'guia/primeros-pasos': 4}
used = set()
for file in sorted((WIKI / 'paginas').rglob('*.php')):
    article = Article()
    article.feed(file.read_text(encoding='utf8'))
    key = file.relative_to(WIKI / 'paginas').with_suffix('').as_posix()
    assert len(article.images) >= minimum.get(key, 0), f'Faltan capturas esenciales en {key}'
    assert article.figures == article.captions == len(article.images), f'Figura o pie incompleto en {key}'
    for source in article.images:
        assert source.startswith('img/actuales/'), f'Captura de versión anterior en {key}'
        target = (WIKI / source).resolve()
        assert target.is_relative_to(WIKI / 'img/actuales') and target.is_file(), f'Imagen ausente: {source}'
        assert target.stat().st_size > 1000, f'Archivo incompleto: {source}'
        used.add(target.name)
assert used == expected, 'Las guías y el manifiesto deben usar las mismas capturas'
assert {p.name for p in (WIKI / 'img/actuales').glob('*.jpg')} == expected, 'Archivos de captura sin registrar'
print(f'Wiki visual aprobada: {len(used)} imágenes actuales, con dimensiones, texto alternativo y pies; guías esenciales conservadas.')
