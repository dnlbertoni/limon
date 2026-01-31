#!/usr/bin/env python3
"""Extrae la estructura de módulos CodeIgniter: controllers, models, vistas y tablas.

Genera `backend/db_mappings/all_modules_map.yml` con el siguiente formato:

modules:
  articulos:
    controllers: [Articulos.php, ...]
    models:
      - file: articulos_model.php
        class: Articulos_model
        table: tbl_articulos
        primary_key: id_articulo
    views:
      - file: index.php
        uses_keyboard_js: true

Uso:
  python backend/scripts/extract_ci_structure.py

El script busca heurísticas en los archivos PHP para inferir nombres de tablas y claves primarias.
"""
import os
import re
import yaml
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]  # repo root
# Scan both current and deprecated CodeIgniter apps
MODULES_DIRS = [
    ROOT / 'www' / 'application' / 'modules',
    ROOT / 'deprecar' / 'www' / 'application' / 'modules'
]
OUT_FILE = ROOT / 'backend' / 'db_mappings' / 'all_modules_map.yml'


def list_php_files(folder):
    return [f for f in sorted(os.listdir(folder)) if f.endswith('.php')]


def parse_class_name(content):
    m = re.search(r'class\s+([A-Za-z0-9_]+)\s+extends', content)
    return m.group(1) if m else None


def parse_var_tabla(content):
    # busca var $tabla = array(... 'name' => 'tbl_name' ... );
    m = re.search(r'var\s+\$tabla\s*=\s*array\((.*?)\);', content, re.S)
    if not m:
        return None
    inner = m.group(1)
    mm = re.search(r"['\"]name['\"]\s*=>\s*['\"]([A-Za-z0-9_]+)['\"]", inner)
    if mm:
        return mm.group(1)
    return None


def parse_primary_key(content):
    m = re.search(r"primaryKey\s*=\s*['\"]?([A-Za-z0-9_]+)['\"]?", content)
    if m:
        return m.group(1)
    m2 = re.search(r"primary_key\s*=\s*['\"]?([A-Za-z0-9_]+)['\"]?", content)
    if m2:
        return m2.group(1)
    # buscar var $primaryKey
    m3 = re.search(r"var\s+\$primaryKey\s*=\s*['\"]?([A-Za-z0-9_]+)['\"]?", content)
    if m3:
        return m3.group(1)
    # heurística: buscar ID_... may appear in code
    m4 = re.search(r"ID_[A-Za-z0-9_]+", content)
    if m4:
        return m4.group(0)
    return None


def find_tables_in_code(content):
    tables = set()
    for m in re.finditer(r"from\s*\(\s*['\"]([A-Za-z0-9_]+)['\"]\s*\)", content):
        tables.add(m.group(1))
    for m in re.finditer(r"join\s*\(\s*['\"]([A-Za-z0-9_]+)['\"]\s*,", content):
        tables.add(m.group(1))
    for m in re.finditer(r"->setTable\(\s*['\"]([A-Za-z0-9_]+)['\"]\s*\)", content):
        tables.add(m.group(1))
    return list(tables)


def view_uses_keyboard_js(path):
    try:
        txt = Path(path).read_text(encoding='utf-8', errors='ignore')
    except Exception:
        return False
    patterns = ['onkeydown', 'onkeyup', 'onkeypress', 'addEventListener(\'keydown\'', 'document.onkeydown', 'keyCode', 'event.key']
    return any(p in txt for p in patterns)


def scan_module(module_path):
    result = {'controllers': [], 'models': [], 'views': []}
    # controllers
    cdir = os.path.join(module_path, 'controllers')
    if os.path.isdir(cdir):
        for fname in list_php_files(cdir):
            fpath = os.path.join(cdir, fname)
            try:
                content = Path(fpath).read_text(encoding='utf-8', errors='ignore')
            except Exception:
                content = ''
            cls = parse_class_name(content) or fname
            result['controllers'].append({'file': fname, 'class': cls})
    # models
    mdir = os.path.join(module_path, 'models')
    if os.path.isdir(mdir):
        for fname in list_php_files(mdir):
            fpath = os.path.join(mdir, fname)
            try:
                content = Path(fpath).read_text(encoding='utf-8', errors='ignore')
            except Exception:
                content = ''
            cls = parse_class_name(content) or fname
            table = parse_var_tabla(content)
            if not table:
                tables = find_tables_in_code(content)
                table = tables[0] if tables else None
            pk = parse_primary_key(content)
            result['models'].append({'file': fname, 'class': cls, 'table': table, 'primary_key': pk})
    # views
    vdir = os.path.join(module_path, 'views')
    if os.path.isdir(vdir):
        for root, _, files in os.walk(vdir):
            for fname in sorted(files):
                if not fname.lower().endswith(('.php', '.html', '.js')):
                    continue
                fpath = os.path.join(root, fname)
                rel = os.path.relpath(fpath, module_path)
                uses = view_uses_keyboard_js(fpath)
                result['views'].append({'file': rel, 'uses_keyboard_js': uses})
    return result


def main():
    modules = {}
    for MODULES_DIR in MODULES_DIRS:
        if not MODULES_DIR.exists():
            continue
        for module in sorted(os.listdir(MODULES_DIR)):
            mod_path = MODULES_DIR / module
            if not mod_path.is_dir():
                continue
            key = module
            # if scanning deprecated path, prefix key to avoid collisions
            if 'deprecar' in str(MODULES_DIR):
                key = f"legacy/{module}"
            modules[key] = scan_module(str(mod_path))

    out = {'modules': modules}
    OUT_FILE.parent.mkdir(parents=True, exist_ok=True)
    with open(OUT_FILE, 'w', encoding='utf-8') as f:
        yaml.safe_dump(out, f, sort_keys=False, allow_unicode=True)
    print('Mapping generado en:', OUT_FILE)


if __name__ == '__main__':
    main()
