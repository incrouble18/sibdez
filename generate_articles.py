import markdown
import re
from datetime import date

def generate_article_html(template_path, content_path, output_path, title, description, keywords, image_src, image_alt, article_url):
    """Generates a full HTML article from a template and Markdown content."""
    
    # 1. Read template and content
    with open(template_path, 'r', encoding='utf-8') as f:
        template = f.read()
    
    with open(content_path, 'r', encoding='utf-8') as f:
        md_content = f.read()
        
    # Convert Markdown to HTML
    html_content = markdown.markdown(md_content)
    
    # 2. Extract article body from template
    # Assuming the article body is between <!-- ТЕЛО СТАТЬИ ВСТАВЛЯЕТСЯ СЮДА --> and <!-- КОНЕЦ ТЕЛА СТАТЬИ -->
    # Since the template is truncated, I'll rely on the known structure to inject the content into the article-body div.
    
    # Find the article body section
    body_start_tag = '<div class="article-body">'
    body_end_tag = '<!-- КОНЕЦ ТЕЛА СТАТЬИ -->' # Assuming this is the end marker
    
    # A safer way is to find the article-body div and replace its content
    
    # 3. Replace placeholders in the template
    
    # Title and Meta
    template = template.replace('ЗАГОЛОВОК СТАТЬИ', title)
    template = template.replace('КРАТКОЕ ОПИСАНИЕ СТАТЬИ', description)
    template = template.replace('КЛЮЧЕВЫЕ СЛОВА', keywords)
    
    # Canonical URL
    template = template.replace('article-1.html', article_url)
    
    # Image
    template = template.replace('images/article-placeholder.jpg', image_src)
    template = template.replace('Изображение статьи', image_alt)
    
    # Date
    today = date.today().strftime("%d.%m.%Y")
    template = template.replace('ДАТА ПУБЛИКАЦИИ', today)
    
    # Content injection
    # Find the article-body div and inject the HTML content
    # The template has a placeholder: <!-- ТЕЛО СТАТЬИ ВСТАВЛЯЕТСЯ СЮДА -->
    # I will replace the entire article-body div content
    
    # Since I don't have the full template, I will assume the structure is:
    # <div class="article-body">
    #     <!-- ТЕЛО СТАТЬИ ВСТАВЛЯЕТСЯ СЮДА -->
    # </div>
    
    # I will search for the placeholder and replace it with the content
    
    # Re-reading the template from the last read:
    # 309	        .article-body p {
    # ...
    # 351	<body>
    # ...
    # 418	        <div class="article-body">
    # 419	            <!-- ТЕЛО СТАТЬИ ВСТАВЛЯЕТСЯ СЮДА -->
    # 420	        </div>
    
    # I will assume the template has a placeholder for the body content:
    
    # The template is truncated, so I will rely on the placeholder from the template read:
    # <div class="article-body">
    #     <!-- ТЕЛО СТАТЬИ ВСТАВЛЯЕТСЯ СЮДА -->
    # </div>
    
    # I will use a simple string replacement for the content placeholder
    template = template.replace('<!-- ТЕЛО СТАТЬИ ВСТАВЛЯЕТСЯ СЮДА -->', html_content)
    
    # 4. Write the final HTML
    with open(output_path, 'w', encoding='utf-8') as f:
        f.write(template)

# --- Article 1 Data ---
article_1_title = "Как убрать запах после травли клопов и тараканов: 5 шагов к свежести"
article_1_desc = "Подробная инструкция от СЭС СибДез Омск о том, как безопасно и эффективно устранить запах после дезинсекции клопов и тараканов, не нарушив барьерную защиту."
article_1_keywords = "как убрать запах после травли клопов, запах после дезинсекции, вонь после тараканов, сухой туман от запаха, проветривание после обработки, чем отмыть квартиру после клопов"
article_1_image_src = "images/article-8-cover.jpg"
article_1_image_alt = "Уборка после дезинсекции"
article_1_url = "article-8.html"

# --- Article 2 Data ---
article_2_title = "Народные средства от тараканов: Мифы и Правда. Что реально работает, а что — пустая трата времени?"
article_2_desc = "Экспертный разбор популярных народных средств (борная кислота, сода, нашатырь) против тараканов и объяснение, почему профессиональная дезинсекция необходима."
article_2_keywords = "народные средства от тараканов, борная кислота от тараканов, сода и сахар от тараканов, нашатырный спирт от тараканов, что не любят тараканы, как избавиться от тараканов навсегда"
article_2_image_src = "images/article-9-cover.jpg"
article_2_image_alt = "Народные средства против тараканов"
article_2_url = "article-9.html"

# --- Paths ---
template_path = 'article-template.html'
content_1_path = 'article_1_content.md'
output_1_path = 'article-8.html'
content_2_path = 'article_2_content.md'
output_2_path = 'article-9.html'

# --- Execution ---
try:
    generate_article_html(template_path, content_1_path, output_1_path, article_1_title, article_1_desc, article_1_keywords, article_1_image_src, article_1_image_alt, article_1_url)
    print(f"Successfully generated {output_1_path}")
    
    generate_article_html(template_path, content_2_path, output_2_path, article_2_title, article_2_desc, article_2_keywords, article_2_image_src, article_2_image_alt, article_2_url)
    print(f"Successfully generated {output_2_path}")

except Exception as e:
    print(f"An error occurred: {e}")

# Clean up temporary content files
import os
os.remove(content_1_path)
os.remove(content_2_path)
print("Cleaned up temporary content files.")
