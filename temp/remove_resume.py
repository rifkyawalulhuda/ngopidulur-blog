import re

file_path = r'D:\Github\ngopidulur-blog\resources\js\admin.js'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Remove resume from pageFromPath
content = re.sub(r"\s*if \(path\.startsWith\('/admin/resume'\)\) \{\s*return 'resume';\s*\}", '', content)

# Remove resume data object
content = re.sub(r'resume: \{[^}]*\},', '', content, flags=re.DOTALL)

# Remove resume from pageTitle
content = re.sub(r"\s*if \(this\.current === 'resume'\) \{\s*return 'Resume';\s*\}", '', content)

# Remove resume from pageDescription
content = re.sub(r"\s*if \(this\.current === 'resume'\) \{[^}]*\}", '', content)

# Remove loadResume call
content = re.sub(r"\s*if \(this\.current === 'resume'\) \{\s*await this\.loadResume\(\);\s*\}", '', content)

# Remove loadResume and saveResume methods
content = re.sub(r'async loadResume\(\) \{[^}]*\}\s*async saveResume\(\) \{[^}]*\}', '', content, flags=re.DOTALL)

# Remove resume section from template
content = re.sub(r'<section v-else-if=\"current === \'resume\'\"[^<]*(?:<(?!section)[^<]*)*</section>', '', content, flags=re.DOTALL)

with open(file_path, 'w', encoding='utf-8', newline='\n') as f:
    f.write(content)

print('Resume references removed successfully')
