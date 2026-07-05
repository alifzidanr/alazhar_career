<x-layouts.public :title="'Tentang Kami - Rekrutmen YPI Al Azhar'">
    <section class="border-b bg-gradient-to-b from-muted/60 to-background">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Tentang Kami</h1>
            <p class="mt-4 text-muted-foreground leading-relaxed">
                Yayasan Pendidikan Islam Al Azhar hadir untuk mewujudkan standar keunggulan pendidikan Islam di Indonesia
                melalui seleksi yang profesional serta pengembangan talenta terbaik.
            </p>
        </div>
    </section>

    <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid gap-6 sm:grid-cols-3">
            <x-ui.card>
                <h3 class="font-semibold">Visi</h3>
                <p class="mt-2 text-sm text-muted-foreground leading-relaxed">Membangun masa depan generasi Qur'ani melalui pendidikan Islam yang unggul dan berkarakter.</p>
            </x-ui.card>
            <x-ui.card>
                <h3 class="font-semibold">Misi</h3>
                <p class="mt-2 text-sm text-muted-foreground leading-relaxed">Menyelenggarakan pendidikan berkualitas dan merekrut pendidik serta profesional terbaik yang visioner.</p>
            </x-ui.card>
            <x-ui.card>
                <h3 class="font-semibold">Nilai</h3>
                <p class="mt-2 text-sm text-muted-foreground leading-relaxed">Profesionalisme, integritas, dan komitmen terhadap keunggulan dalam setiap proses seleksi dan pengembangan talenta.</p>
            </x-ui.card>
        </div>

        <div class="mt-10 text-center">
            <x-ui.button :href="route('loker.index')" size="lg">Jelajahi Karir Bersama Kami</x-ui.button>
        </div>
    </section>
</x-layouts.public>
