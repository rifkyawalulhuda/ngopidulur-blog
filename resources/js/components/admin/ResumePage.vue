<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
    resume: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['save-resume']);

const addExperience = () => {
    props.resume.experience.push({
        position: '',
        company: '',
        location: '',
        period: '',
        items: [],
    });
};

const removeExperience = (index) => {
    props.resume.experience.splice(index, 1);
};

const addExperienceItem = (expIndex) => {
    props.resume.experience[expIndex].items.push('');
};

const removeExperienceItem = (expIndex, itemIndex) => {
    props.resume.experience[expIndex].items.splice(itemIndex, 1);
};

const addProject = () => {
    props.resume.projects.push({
        name: '',
        period: '',
        description: '',
        tech: '',
    });
};

const removeProject = (index) => {
    props.resume.projects.splice(index, 1);
};

const addSkill = () => {
    const newSkill = prompt('Masukkan keahlian baru:');
    if (newSkill && newSkill.trim()) {
        props.resume.skills.push(newSkill.trim());
    }
};

const removeSkill = (index) => {
    props.resume.skills.splice(index, 1);
};

const addCertification = () => {
    const newCert = prompt('Masukkan sertifikasi baru:');
    if (newCert && newCert.trim()) {
        props.resume.certifications.push(newCert.trim());
    }
};

const removeCertification = (index) => {
    props.resume.certifications.splice(index, 1);
};
</script>

<template>
    <section class="space-y-6">
        <div v-if="resume.loading" class="grid gap-4">
            <div v-for="n in 4" :key="n" class="h-20 animate-pulse rounded-2xl bg-gray-50 dark:bg-white/5"></div>
        </div>

        <form v-else @submit.prevent="emit('save-resume')" class="space-y-6">
            <!-- Header Info -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Informasi Dasar</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Data profil yang ditampilkan di halaman Tentang.</p>

                <div class="mt-6 grid gap-5 sm:grid-cols-2">
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Nama Lengkap *</span>
                        <input v-model="resume.name" type="text" required class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" placeholder="Nama lengkap">
                    </label>

                    <label class="space-y-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Judul/Posisi *</span>
                        <input v-model="resume.title" type="text" required class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" placeholder="IT Support Engineer">
                    </label>

                    <label class="space-y-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Lokasi</span>
                        <input v-model="resume.location" type="text" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" placeholder="Kota, Negara">
                    </label>

                    <label class="space-y-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Email</span>
                        <input v-model="resume.email" type="email" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" placeholder="email@example.com">
                    </label>
                </div>

                <label class="mt-5 space-y-2">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Ringkasan</span>
                    <textarea v-model="resume.summary" rows="3" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" placeholder="Deskripsi singkat tentang diri Anda"></textarea>
                </label>
            </div>

            <!-- Experience -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Pengalaman Kerja</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Riwayat pekerjaan dan tanggung jawab.</p>
                    </div>
                    <button @click="addExperience" type="button" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-brand-600">Tambah</button>
                </div>

                <div v-for="(exp, expIndex) in resume.experience" :key="expIndex" class="mt-6 space-y-4 rounded-xl border border-gray-200 p-5 dark:border-gray-700">
                    <div class="flex items-start justify-between">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Pengalaman #{{ expIndex + 1 }}</span>
                        <button @click="removeExperience(expIndex)" type="button" class="text-sm text-red-600 hover:text-red-700 dark:text-red-400">Hapus</button>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="space-y-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Posisi</span>
                            <input v-model="exp.position" type="text" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
                        </label>

                        <label class="space-y-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Periode</span>
                            <input v-model="exp.period" type="text" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" placeholder="Jan 2020 - Sekarang">
                        </label>

                        <label class="space-y-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Perusahaan</span>
                            <input v-model="exp.company" type="text" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
                        </label>

                        <label class="space-y-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Lokasi</span>
                            <input v-model="exp.location" type="text" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
                        </label>
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Tanggung Jawab</span>
                            <button @click="addExperienceItem(expIndex)" type="button" class="text-sm text-brand-600 hover:text-brand-700 dark:text-brand-400">+ Tambah Item</button>
                        </div>
                        <div v-for="(item, itemIndex) in exp.items" :key="itemIndex" class="mt-2 flex gap-2">
                            <input v-model="exp.items[itemIndex]" type="text" class="flex-1 rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
                            <button @click="removeExperienceItem(expIndex, itemIndex)" type="button" class="text-red-600 hover:text-red-700 dark:text-red-400">Hapus</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Projects -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Proyek</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Proyek web app atau portfolio.</p>
                    </div>
                    <button @click="addProject" type="button" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-brand-600">Tambah</button>
                </div>

                <div v-for="(project, projIndex) in resume.projects" :key="projIndex" class="mt-6 space-y-4 rounded-xl border border-gray-200 p-5 dark:border-gray-700">
                    <div class="flex items-start justify-between">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Proyek #{{ projIndex + 1 }}</span>
                        <button @click="removeProject(projIndex)" type="button" class="text-sm text-red-600 hover:text-red-700 dark:text-red-400">Hapus</button>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="space-y-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Nama Proyek</span>
                            <input v-model="project.name" type="text" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
                        </label>

                        <label class="space-y-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Periode</span>
                            <input v-model="project.period" type="text" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" placeholder="Jan - Mar 2024">
                        </label>
                    </div>

                    <label class="space-y-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Deskripsi</span>
                        <textarea v-model="project.description" rows="2" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200"></textarea>
                    </label>

                    <label class="space-y-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Teknologi</span>
                        <input v-model="project.tech" type="text" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" placeholder="Node.js Â· Vue Â· PostgreSQL">
                    </label>
                </div>
            </div>

            <!-- Skills -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Keahlian</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Daftar keahlian teknis dan soft skills.</p>
                    </div>
                    <button @click="addSkill" type="button" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-brand-600">Tambah</button>
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    <div v-for="(skill, index) in resume.skills" :key="index" class="flex items-center gap-2 rounded-full border border-gray-300 bg-gray-50 px-4 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
                        <span class="text-gray-700 dark:text-gray-200">{{ skill }}</span>
                        <button @click="removeSkill(index)" type="button" class="text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400">Hapus</button>
                    </div>
                </div>
            </div>

            <!-- Education -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Pendidikan</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Riwayat pendidikan formal.</p>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Gelar</span>
                        <input v-model="resume.education.degree" type="text" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" placeholder="Sarjana (S1)">
                    </label>

                    <label class="space-y-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Institusi</span>
                        <input v-model="resume.education.institution" type="text" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
                    </label>

                    <label class="space-y-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Periode</span>
                        <input v-model="resume.education.period" type="text" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" placeholder="2017 - 2021">
                    </label>

                    <label class="space-y-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">IPK</span>
                        <input v-model="resume.education.gpa" type="text" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" placeholder="3.50">
                    </label>
                </div>
            </div>

            <!-- Certifications -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Sertifikasi</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Sertifikat dan pelatihan yang telah diselesaikan.</p>
                    </div>
                    <button @click="addCertification" type="button" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-brand-600">Tambah</button>
                </div>

                <ul class="mt-4 space-y-2">
                    <li v-for="(cert, index) in resume.certifications" :key="index" class="flex items-center justify-between rounded-lg border border-gray-200 px-4 py-3 dark:border-gray-700">
                        <span class="text-sm text-gray-700 dark:text-gray-200">{{ cert }}</span>
                        <button @click="removeCertification(index)" type="button" class="text-red-600 hover:text-red-700 dark:text-red-400">Hapus</button>
                    </li>
                </ul>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3">
                <button type="submit" :disabled="resume.saving" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-6 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600 disabled:opacity-50">
                    <span v-if="resume.saving">Menyimpan...</span>
                    <span v-else>Simpan Resume</span>
                </button>
            </div>
        </form>
    </section>
</template>