<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestion des finances personnelles</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'] } } } };
    </script>
    <style>
        [v-cloak]{display:none}
        body{background:linear-gradient(180deg,#f8fafc 0%,#eef2ff 100%)}
    </style>
</head>
<body class="font-sans text-slate-900">
<div id="app" v-cloak class="min-h-screen">
    <div class="min-h-screen lg:flex">
        <aside class="lg:w-72 bg-slate-950 text-slate-100 p-6 lg:min-h-screen">
            <div class="flex items-center gap-3 mb-10">
                <div class="h-11 w-11 rounded-2xl bg-indigo-500/20 flex items-center justify-center text-indigo-300">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <div>
                    <div class="text-xs uppercase tracking-[0.3em] text-slate-400">Personal Finance</div>
                    <h1 class="font-semibold text-lg">Finance Control</h1>
                </div>
            </div>
            <nav class="space-y-2">
                <button v-for="tab in tabs" :key="tab.key" @click="activeTab = tab.key"
                    class="w-full flex items-center gap-3 rounded-2xl px-4 py-3 text-left transition"
                    :class="activeTab === tab.key ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white'">
                    <i :class="tab.icon"></i><span class="font-medium">@{{ tab.label }}</span>
                </button>
            </nav>
            <div class="mt-10 rounded-3xl bg-white/5 p-4">
                <div class="text-sm text-slate-400">Utilisateur connecté</div>
                <div class="mt-1 font-semibold">@{{ auth.user ? auth.user.prenom + ' ' + auth.user.nom : 'Invité' }}</div>
            </div>
        </aside>

        <main class="flex-1">
            <header class="sticky top-0 z-20 backdrop-blur bg-white/80 border-b border-slate-200">
                <div class="px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Gestion des finances personnelles</p>
                        <h2 class="text-xl font-semibold">@{{ title }}</h2>
                    </div>
                    <button v-if="auth.user" @click="logout" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 text-white px-4 py-2.5 text-sm font-medium hover:bg-slate-700">
                        <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
                    </button>
                </div>
            </header>

            <section class="px-4 sm:px-6 lg:px-8 py-8">
                <div v-if="!auth.user" class="max-w-5xl mx-auto grid lg:grid-cols-2 gap-6">
                    <div class="rounded-[2rem] bg-slate-950 text-white p-8 shadow-2xl">
                        <p class="text-sm text-slate-400">Module 1</p>
                        <h3 class="mt-2 text-3xl font-semibold">Authentification</h3>
                        <p class="mt-4 text-slate-300">Connexion sécurisée par session pour accéder aux modules autorisés.</p>
                    </div>
                    <div class="rounded-[2rem] bg-white p-8 shadow-sm border border-slate-200">
                        <div class="flex gap-2 mb-6">
                            <button @click="authMode='login'" class="px-4 py-2 rounded-xl text-sm font-medium" :class="authMode==='login'?'bg-slate-900 text-white':'bg-slate-100'">Connexion</button>
                            <button @click="authMode='register'" class="px-4 py-2 rounded-xl text-sm font-medium" :class="authMode==='register'?'bg-slate-900 text-white':'bg-slate-100'">Inscription</button>
                        </div>

                        <form v-if="authMode==='login'" @submit.prevent="login" class="space-y-4">
                            <input v-model="loginForm.email" type="email" placeholder="Email" class="w-full rounded-2xl border-slate-200 px-4 py-3">
                            <input v-model="loginForm.mot_de_passe" type="password" placeholder="Mot de passe" class="w-full rounded-2xl border-slate-200 px-4 py-3">
                            <button class="w-full rounded-2xl bg-indigo-600 text-white py-3 font-medium">Se connecter</button>
                        </form>

                        <form v-else @submit.prevent="register" class="space-y-4">
                            <div class="grid sm:grid-cols-2 gap-4">
                                <input v-model="registerForm.nom" type="text" placeholder="Nom" class="w-full rounded-2xl border-slate-200 px-4 py-3">
                                <input v-model="registerForm.prenom" type="text" placeholder="Prénom" class="w-full rounded-2xl border-slate-200 px-4 py-3">
                            </div>
                            <input v-model="registerForm.email" type="email" placeholder="Email" class="w-full rounded-2xl border-slate-200 px-4 py-3">
                            <input v-model="registerForm.mot_de_passe" type="password" placeholder="Mot de passe" class="w-full rounded-2xl border-slate-200 px-4 py-3">
                            <input v-model="registerForm.mot_de_passe_confirmation" type="password" placeholder="Confirmation" class="w-full rounded-2xl border-slate-200 px-4 py-3">
                            <button class="w-full rounded-2xl bg-slate-900 text-white py-3 font-medium">Créer le compte</button>
                        </form>
                    </div>
                </div>

                <div v-else class="space-y-8">
                    <div class="grid md:grid-cols-3 gap-4">
                        <div class="rounded-3xl bg-white border border-slate-200 p-5 shadow-sm"><div class="text-sm text-slate-500">Catégories</div><div class="mt-2 text-3xl font-semibold">@{{ categories.items.length }}</div></div>
                        <div class="rounded-3xl bg-white border border-slate-200 p-5 shadow-sm"><div class="text-sm text-slate-500">Revenus</div><div class="mt-2 text-3xl font-semibold">@{{ revenus.items.length }}</div></div>
                        <div class="rounded-3xl bg-white border border-slate-200 p-5 shadow-sm"><div class="text-sm text-slate-500">Dépenses</div><div class="mt-2 text-3xl font-semibold">@{{ depenses.items.length }}</div></div>
                    </div>

                    <div v-if="activeTab==='categories'" class="grid lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-1 rounded-3xl bg-white border border-slate-200 p-6">
                            <h3 class="font-semibold text-lg mb-4">Catégorie</h3>
                            <form @submit.prevent="saveCategory" class="space-y-4">
                                <input v-model="categoryForm.nom_categorie" type="text" placeholder="Nom de catégorie" class="w-full rounded-2xl border-slate-200 px-4 py-3">
                                <button class="w-full rounded-2xl bg-slate-900 text-white py-3">Enregistrer</button>
                            </form>
                        </div>
                        <div class="lg:col-span-2 rounded-3xl bg-white border border-slate-200 p-6">
                            <div class="flex items-center justify-between gap-4 mb-4">
                                <input v-model="categories.search" @input="debouncedLoadCategories" placeholder="Rechercher" class="w-full max-w-sm rounded-2xl border-slate-200 px-4 py-3">
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="text-slate-500"><tr><th class="text-left py-3">Nom</th><th class="text-right py-3">Actions</th></tr></thead>
                                    <tbody>
                                        <tr v-for="item in categories.items" :key="item.id_categorie" class="border-t">
                                            <td class="py-4">@{{ item.nom_categorie }}</td>
                                            <td class="py-4 text-right">
                                                <button @click="editCategory(item)" class="text-indigo-600 mr-3">Modifier</button>
                                                <button @click="destroy('categories', item.id_categorie)" class="text-rose-600">Supprimer</button>
                                            </td>
                                        </tr>
                                        <tr v-if="!categories.items.length"><td colspan="2" class="py-10 text-center text-slate-500">Aucune catégorie.</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div v-if="activeTab==='revenus'" class="rounded-3xl bg-white border border-slate-200 p-6 space-y-6">
                        <div class="grid lg:grid-cols-3 gap-4">
                            <input v-model="revenuForm.source" placeholder="Source" class="rounded-2xl border-slate-200 px-4 py-3">
                            <input v-model="revenuForm.montant" type="number" step="0.01" placeholder="Montant" class="rounded-2xl border-slate-200 px-4 py-3">
                            <input v-model="revenuForm.date_revenu" type="date" class="rounded-2xl border-slate-200 px-4 py-3">
                        </div>
                        <textarea v-model="revenuForm.description" placeholder="Description" class="w-full rounded-2xl border-slate-200 px-4 py-3"></textarea>
                        <div class="flex gap-3">
                            <button @click="saveRevenu" class="rounded-2xl bg-slate-900 text-white px-5 py-3">Enregistrer</button>
                            <input v-model="revenus.search" @input="debouncedLoadRevenus" placeholder="Rechercher" class="rounded-2xl border-slate-200 px-4 py-3 flex-1">
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="text-slate-500"><tr><th class="text-left py-3">Source</th><th class="text-left py-3">Date</th><th class="text-left py-3">Montant</th><th></th></tr></thead>
                                <tbody>
                                    <tr v-for="item in revenus.items" :key="item.id_revenu" class="border-t">
                                        <td class="py-4">@{{ item.source }}</td><td>@{{ item.date_revenu }}</td><td>@{{ item.montant }}</td>
                                        <td class="text-right">
                                            <button @click="editRevenu(item)" class="text-indigo-600 mr-3">Modifier</button>
                                            <button @click="destroy('revenus', item.id_revenu)" class="text-rose-600">Supprimer</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div v-if="activeTab==='depenses'" class="rounded-3xl bg-white border border-slate-200 p-6 space-y-6">
                        <div class="grid lg:grid-cols-4 gap-4">
                            <select v-model="depenseForm.id_categorie" class="rounded-2xl border-slate-200 px-4 py-3">
                                <option value="">Catégorie</option>
                                <option v-for="item in categories.items" :key="item.id_categorie" :value="item.id_categorie">@{{ item.nom_categorie }}</option>
                            </select>
                            <input v-model="depenseForm.montant" type="number" step="0.01" placeholder="Montant" class="rounded-2xl border-slate-200 px-4 py-3">
                            <input v-model="depenseForm.date_depense" type="date" class="rounded-2xl border-slate-200 px-4 py-3">
                            <button @click="saveDepense" class="rounded-2xl bg-slate-900 text-white px-5 py-3">Enregistrer</button>
                        </div>
                        <textarea v-model="depenseForm.description" placeholder="Description" class="w-full rounded-2xl border-slate-200 px-4 py-3"></textarea>
                        <div class="flex gap-3">
                            <input v-model="depenses.search" @input="debouncedLoadDepenses" placeholder="Rechercher" class="rounded-2xl border-slate-200 px-4 py-3 flex-1">
                            <select v-model="depenses.sort" @change="loadDepenses" class="rounded-2xl border-slate-200 px-4 py-3">
                                <option value="date_depense">Date</option><option value="id_depense">Récent</option>
                            </select>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="text-slate-500"><tr><th class="text-left py-3">Catégorie</th><th class="text-left py-3">Date</th><th class="text-left py-3">Montant</th><th></th></tr></thead>
                                <tbody>
                                    <tr v-for="item in depenses.items" :key="item.id_depense" class="border-t">
                                        <td class="py-4">@{{ item.categorie ? item.categorie.nom_categorie : '-' }}</td><td>@{{ item.date_depense }}</td><td>@{{ item.montant }}</td>
                                        <td class="text-right">
                                            <button @click="editDepense(item)" class="text-indigo-600 mr-3">Modifier</button>
                                            <button @click="destroy('depenses', item.id_depense)" class="text-rose-600">Supprimer</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.prod.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;
const api = axios.create({ baseURL: '/api' });
const debounce = (fn, wait = 300) => { let t; return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), wait); }; };

Vue.createApp({
    data() {
        return {
            activeTab: 'categories',
            authMode: 'login',
            auth: { user: null },
            tabs: [
                { key: 'categories', label: 'Catégories', icon: 'fa-solid fa-tags' },
                { key: 'revenus', label: 'Revenus', icon: 'fa-solid fa-coins' },
                { key: 'depenses', label: 'Dépenses', icon: 'fa-solid fa-receipt' },
            ],
            loginForm: { email: '', mot_de_passe: '' },
            registerForm: { nom: '', prenom: '', email: '', mot_de_passe: '', mot_de_passe_confirmation: '' },
            categoryForm: { id_categorie: null, nom_categorie: '' },
            revenuForm: { id_revenu: null, source: '', montant: '', date_revenu: '', description: '' },
            depenseForm: { id_depense: null, id_categorie: '', montant: '', date_depense: '', description: '' },
            categories: { items: [], search: '' },
            revenus: { items: [], search: '', sort: 'date_revenu', direction: 'desc' },
            depenses: { items: [], search: '', sort: 'date_depense', direction: 'desc' },
            debouncedLoadCategories: null,
            debouncedLoadRevenus: null,
            debouncedLoadDepenses: null,
        };
    },
    computed: {
        title() {
            return this.tabs.find(tab => tab.key === this.activeTab)?.label ?? 'Accueil';
        }
    },
    mounted() {
        this.debouncedLoadCategories = debounce(() => this.loadCategories(), 250);
        this.debouncedLoadRevenus = debounce(() => this.loadRevenus(), 250);
        this.debouncedLoadDepenses = debounce(() => this.loadDepenses(), 250);
        this.bootstrap();
    },
    methods: {
        async bootstrap() {
            try {
                const { data } = await api.get('/auth/me');
                this.auth.user = data.data;
                await this.loadAll();
            } catch (e) {}
        },
        async loadAll() {
            await Promise.all([this.loadCategories(), this.loadRevenus(), this.loadDepenses()]);
        },
        async login() {
            const { data } = await api.post('/auth/login', this.loginForm);
            this.auth.user = data.data;
            toastr.success(data.message);
            await this.loadAll();
        },
        async register() {
            const { data } = await api.post('/auth/register', this.registerForm);
            this.auth.user = data.data;
            toastr.success(data.message);
            await this.loadAll();
        },
        async logout() {
            await api.post('/auth/logout');
            this.auth.user = null;
        },
        async loadCategories() {
            const { data } = await api.get('/categories', { params: { search: this.categories.search } });
            this.categories.items = data.data ?? [];
        },
        async saveCategory() {
            const payload = { nom_categorie: this.categoryForm.nom_categorie };
            if (this.categoryForm.id_categorie) await api.put(`/categories/${this.categoryForm.id_categorie}`, payload);
            else await api.post('/categories', payload);
            this.categoryForm = { id_categorie: null, nom_categorie: '' };
            await this.loadCategories();
        },
        editCategory(item) { this.categoryForm = { ...item }; this.activeTab = 'categories'; },
        async loadRevenus() {
            const { data } = await api.get('/revenus', { params: { search: this.revenus.search, sort: this.revenus.sort, direction: this.revenus.direction } });
            this.revenus.items = data.data ?? [];
        },
        async saveRevenu() {
            const payload = { ...this.revenuForm };
            if (payload.id_revenu) await api.put(`/revenus/${payload.id_revenu}`, payload);
            else await api.post('/revenus', payload);
            this.revenuForm = { id_revenu: null, source: '', montant: '', date_revenu: '', description: '' };
            await this.loadRevenus();
        },
        editRevenu(item) { this.revenuForm = { ...item }; this.activeTab = 'revenus'; },
        async loadDepenses() {
            const { data } = await api.get('/depenses', { params: { search: this.depenses.search, sort: this.depenses.sort, direction: this.depenses.direction } });
            this.depenses.items = data.data ?? [];
        },
        async saveDepense() {
            const payload = { ...this.depenseForm };
            if (payload.id_depense) await api.put(`/depenses/${payload.id_depense}`, payload);
            else await api.post('/depenses', payload);
            this.depenseForm = { id_depense: null, id_categorie: '', montant: '', date_depense: '', description: '' };
            await this.loadDepenses();
        },
        editDepense(item) { this.depenseForm = { ...item }; this.activeTab = 'depenses'; },
        async destroy(resource, id) {
            const ok = await Swal.fire({ title: 'Confirmer', text: 'Action irréversible.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Supprimer' });
            if (!ok.isConfirmed) return;
            await api.delete(`/${resource}/${id}`);
            await this.loadAll();
        },
    }
}).mount('#app');
</script>
</body>
</html>
