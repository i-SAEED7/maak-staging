# Migration Plan 003: Files and Portfolios

## الملفات

1. create_files_table
2. create_file_access_tokens_table
3. create_portfolios_table
4. create_portfolio_items_table

## ملاحظات

- `files` يسبق `portfolio_items` لأن العنصر قد يشير إلى ملف.
