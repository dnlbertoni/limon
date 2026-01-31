"""Generador sencillo de modelos SQLAlchemy a partir de mappings YAML.

Uso:
  python backend/scripts/generate_models.py backend/db_mappings/articulos_mapping.yml

El script imprime stubs que pueden copiarse a `backend/app/models/`.
Los modelos usan `__tablename__` y `Column` con nombres originales para facilitar la migración.
"""
import sys
import yaml
from textwrap import indent


TEMPLATE = '''from sqlalchemy import Column, Integer, String, Text, Numeric, DateTime
from sqlalchemy.orm import declarative_base

Base = declarative_base()

class {class_name}(Base):
    __tablename__ = "{tablename}"

{columns}

'''

TYPE_MAP = {
    'INTEGER': 'Column(Integer, primary_key={pk})',
    'TEXT': 'Column(Text)',
    'NUMERIC': 'Column(Numeric)',
    'VARCHAR': 'Column(String)',
    'TIMESTAMP': 'Column(DateTime)'
}


def to_class_name(tablename):
    name = tablename
    name = name.replace('tbl_', '').replace('stk_', '')
    parts = name.split('_')
    return ''.join(p.capitalize() for p in parts)


def generate(mapping):
    out = []
    for table, conf in mapping.items():
        tablename = conf.get('__tablename__', table)
        pk = conf.get('primary_key')
        cols = conf.get('columns', {})
        lines = []
        for col_name, col_type in cols.items():
            col_expr = TYPE_MAP.get(col_type.upper(), 'Column(Text)')
            is_pk = 'True' if col_name == pk else 'False'
            if '{pk}' in col_expr:
                col_line = f"{col_name} = {col_expr.format(pk=is_pk)}"
            else:
                col_line = f"{col_name} = {col_expr}"
            lines.append(col_line)
        class_name = to_class_name(tablename)
        out.append(TEMPLATE.format(class_name=class_name, tablename=tablename, columns=indent('\n'.join(lines), '    ')))
    return '\n'.join(out)


def main():
    if len(sys.argv) < 2:
        print('Usage: generate_models.py <mapping.yml>')
        sys.exit(1)
    path = sys.argv[1]
    with open(path, 'r', encoding='utf-8') as f:
        mapping = yaml.safe_load(f)
    print(generate(mapping))


if __name__ == '__main__':
    main()
