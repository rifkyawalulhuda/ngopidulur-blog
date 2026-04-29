import { createApp } from 'vue';

document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('ngopi-dulur-admin-app');

    if (! root) {
        return;
    }

    const userName = root.dataset.userName || 'Kawan';

    createApp({
        data() {
            return {
                userName,
            };
        },
        template: `
            <section class="space-y-6">
                <div class="overflow-hidden rounded-3xl border border-coffee-100 bg-white shadow-soft dark:border-coffee-800/40 dark:bg-neutralwarm-900">
                    <div class="grid gap-6 px-6 py-8 md:grid-cols-[minmax(0,1fr)_260px] md:px-8">
                        <div class="space-y-4">
                            <span class="inline-flex items-center rounded-full border border-coffee-100 bg-coffee-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:border-coffee-700/40 dark:bg-coffee-500/10 dark:text-coffee-100">
                                Dashboard admin
                            </span>
                            <div class="space-y-2">
                                <h2 class="font-lora text-3xl font-semibold text-coffee-900 dark:text-neutralwarm-50">
                                    Selamat datang kembali ☕
                                </h2>
                                <p class="max-w-xl text-sm leading-6 text-neutralwarm-500 dark:text-neutralwarm-100/80">
                                    Mau nulis apa hari ini?
                                </p>
                            </div>
                            <p class="max-w-2xl text-sm leading-6 text-neutralwarm-500 dark:text-neutralwarm-100/70">
                                Hai, <span class="font-semibold text-coffee-700 dark:text-coffee-100">{{ userName }}</span>.
                                Shell admin sudah siap untuk fase tulisan, kategori, tag, dan media berikutnya.
                            </p>
                        </div>

                        <div class="rounded-2xl border border-coffee-100 bg-coffee-50 p-5 dark:border-coffee-800/40 dark:bg-coffee-500/10">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">
                                Status fondasi
                            </p>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <p class="text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">Navigasi admin</p>
                                    <p class="text-lg font-semibold text-coffee-900 dark:text-neutralwarm-50">Aktif</p>
                                </div>
                                <div>
                                    <p class="text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">Template base</p>
                                    <p class="text-lg font-semibold text-coffee-900 dark:text-neutralwarm-50">TailAdmin</p>
                                </div>
                                <div>
                                    <p class="text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">Brand tone</p>
                                    <p class="text-lg font-semibold text-coffee-900 dark:text-neutralwarm-50">Warm coffee</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        `,
    }).mount(root);
});
