import re

file_path = r'D:\Github\ngopidulur-blog\resources\js\admin.js'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Add ResumePage import after SettingsPage
content = content.replace(
    "const SettingsPage = lazyComponent(() => import('./components/admin/SettingsPage.vue'));",
    "const SettingsPage = lazyComponent(() => import('./components/admin/SettingsPage.vue'));\nconst ResumePage = lazyComponent(() => import('./components/admin/ResumePage.vue'));"
)

# 2. Add resume to pageFromPath before return 'dashboard'
content = content.replace(
    "    return 'dashboard';\n};",
    "    if (path.startsWith('/admin/resume')) {\n        return 'resume';\n    }\n\n    return 'dashboard';\n};",
    1
)

# 3. Add ResumePage to components
content = content.replace(
    "        SettingsPage,\n    },",
    "        SettingsPage,\n        ResumePage,\n    },"
)

# 4. Add resume state to data (after settings object)
settings_end = content.find("            posts: {")
resume_state = '''            resume: {
                loading: true,
                saving: false,
                name: '',
                title: '',
                location: '',
                email: '',
                summary: '',
                experience: [],
                projects: [],
                skills: [],
                education: { degree: '', institution: '', period: '', gpa: '' },
                certifications: [],
            },
'''
content = content[:settings_end] + resume_state + content[settings_end:]

# 5. Add resume to pageTitle computed
content = content.replace(
    "            if (this.current === 'settings') {\n                return 'Pengaturan';\n            }",
    "            if (this.current === 'settings') {\n                return 'Pengaturan';\n            }\n\n            if (this.current === 'resume') {\n                return 'Resume';\n            }"
)

# 6. Add resume to pageDescription computed
content = content.replace(
    "            if (this.current === 'settings') {\n                return 'Atur identitas blog, aset brand, SEO default, dan nuansa public blog dari satu tempat.';\n            }",
    "            if (this.current === 'settings') {\n                return 'Atur identitas blog, aset brand, SEO default, dan nuansa public blog dari satu tempat.';\n            }\n\n            if (this.current === 'resume') {\n                return 'Edit data profil publik yang tampil di halaman Tentang.';\n            }"
)

# 7. Add loadResume call in mounted/init
content = content.replace(
    "            if (this.current === 'settings') {\n                await this.loadSettings();\n            }",
    "            if (this.current === 'settings') {\n                await this.loadSettings();\n            }\n\n            if (this.current === 'resume') {\n                await this.loadResume();\n            }"
)

# 8. Add loadResume and saveResume methods (after loadSettings method)
load_settings_end = content.find("        async loadPosts()")
resume_methods = '''        async loadResume() {
            this.resume.loading = true;

            try {
                const payload = await this.apiCall('/admin/api/resume');
                const item = payload.item || {};
                Object.assign(this.resume, {
                    name: item.name || '',
                    title: item.title || '',
                    location: item.location || '',
                    email: item.email || '',
                    summary: item.summary || '',
                    experience: item.experience || [],
                    projects: item.projects || [],
                    skills: item.skills || [],
                    education: item.education || { degree: '', institution: '', period: '', gpa: '' },
                    certifications: item.certifications || [],
                });
            } catch (error) {
                this.toast(error.message, 'error');
            } finally {
                this.resume.loading = false;
            }
        },

        async saveResume() {
            this.resume.saving = true;

            try {
                const body = {
                    name: this.resume.name,
                    title: this.resume.title,
                    location: this.resume.location,
                    email: this.resume.email,
                    summary: this.resume.summary,
                    experience: this.resume.experience,
                    projects: this.resume.projects,
                    skills: this.resume.skills,
                    education: this.resume.education,
                    certifications: this.resume.certifications,
                };

                const payload = await this.apiCall('/admin/api/resume', {
                    method: 'PUT',
                    body: JSON.stringify(body),
                });

                this.toast(payload.message || 'Resume berhasil disimpan.', 'success');
            } catch (error) {
                this.toast(error.message, 'error');
            } finally {
                this.resume.saving = false;
            }
        },

'''
content = content[:load_settings_end] + resume_methods + content[load_settings_end:]

# 9. Add ResumePage component in template (after settings-page)
settings_page_end = content.find("@save-settings=\"saveSettings\"")
# Find the closing /> after save-settings
closing_tag = content.find("/>", settings_page_end)
next_line = content.find("\n", closing_tag)
resume_template = '''

            <resume-page
                v-else-if="current === 'resume'"
                :resume="resume"
                @save-resume="saveResume" />
'''
content = content[:next_line] + resume_template + content[next_line:]

with open(file_path, 'w', encoding='utf-8', newline='\n') as f:
    f.write(content)

print('Resume added to admin.js successfully')
