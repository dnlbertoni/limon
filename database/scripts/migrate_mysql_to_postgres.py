"""
Database migration script from MySQL to PostgreSQL
"""
import sys
import pymysql
import psycopg2
from psycopg2.extras import execute_batch
import os
from dotenv import load_dotenv

load_dotenv()

# MySQL connection
MYSQL_CONFIG = {
    'host': os.getenv('MYSQL_HOST', 'localhost'),
    'port': int(os.getenv('MYSQL_PORT', 3306)),
    'user': os.getenv('MYSQL_USER', 'root'),
    'password': os.getenv('MYSQL_PASSWORD', ''),
    'database': os.getenv('MYSQL_DATABASE', 'facturador')
}

# PostgreSQL connection
POSTGRES_CONFIG = {
    'host': os.getenv('POSTGRES_HOST', 'localhost'),
    'port': int(os.getenv('POSTGRES_PORT', 5432)),
    'user': os.getenv('POSTGRES_USER', 'limon_user'),
    'password': os.getenv('POSTGRES_PASSWORD', 'limon_password'),
    'database': os.getenv('POSTGRES_DATABASE', 'limon_db')
}


def migrate_table(mysql_conn, pg_conn, table_name, transform_fn=None):
    """
    Migrate a table from MySQL to PostgreSQL
    
    Args:
        mysql_conn: MySQL connection
        pg_conn: PostgreSQL connection
        table_name: Name of the table to migrate
        transform_fn: Optional function to transform data before inserting
    """
    print(f"Migrating table: {table_name}")
    
    # Get data from MySQL
    with mysql_conn.cursor() as cursor:
        cursor.execute(f"SELECT * FROM {table_name}")
        rows = cursor.fetchall()
        columns = [desc[0] for desc in cursor.description]
    
    if not rows:
        print(f"  No data found in {table_name}")
        return
    
    # Transform data if needed
    if transform_fn:
        rows = [transform_fn(row) for row in rows]
    
    # Insert into PostgreSQL
    placeholders = ','.join(['%s'] * len(columns))
    insert_query = f"INSERT INTO {table_name} ({','.join(columns)}) VALUES ({placeholders})"
    
    with pg_conn.cursor() as cursor:
        execute_batch(cursor, insert_query, rows)
        pg_conn.commit()
    
    print(f"  Migrated {len(rows)} rows")


def main():
    """Main migration function"""
    print("Starting database migration from MySQL to PostgreSQL")
    
    # Connect to MySQL
    try:
        mysql_conn = pymysql.connect(**MYSQL_CONFIG)
        print("✓ Connected to MySQL")
    except Exception as e:
        print(f"✗ Error connecting to MySQL: {e}")
        sys.exit(1)
    
    # Connect to PostgreSQL
    try:
        pg_conn = psycopg2.connect(**POSTGRES_CONFIG)
        print("✓ Connected to PostgreSQL")
    except Exception as e:
        print(f"✗ Error connecting to PostgreSQL: {e}")
        mysql_conn.close()
        sys.exit(1)
    
    try:
        # Migrate tables
        # Add your table migrations here
        # Example:
        # migrate_table(mysql_conn, pg_conn, 'users')
        # migrate_table(mysql_conn, pg_conn, 'articles')
        # migrate_table(mysql_conn, pg_conn, 'invoices')
        
        print("\n✓ Migration completed successfully")
    
    except Exception as e:
        print(f"\n✗ Migration failed: {e}")
        pg_conn.rollback()
        sys.exit(1)
    
    finally:
        mysql_conn.close()
        pg_conn.close()
        print("Connections closed")


if __name__ == "__main__":
    main()
