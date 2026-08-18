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
        .loading-bar{background:#2f81f7;box-shadow:0 0 10px rgba(47,129,247,.75),0 0 4px rgba(47,129,247,.9);transition:width .8s cubic-bezier(.22,1,.36,1)}
    </style>
</head>
<body class="font-sans text-slate-900">
<div id="app" v-cloak class="min-h-screen">
    <div v-if="isLoading" class="loading-bar fixed left-0 top-0 z-[100] h-0.5" :style="{ width: loadingProgress + '%' }"></div>
    <div v-if="auth.user && mobileSidebarOpen" @click="mobileSidebarOpen=false" class="fixed inset-0 z-30 bg-slate-950/50 lg:hidden"></div>
    <div class="min-h-screen lg:flex">
        <aside v-if="auth.user" class="fixed inset-y-0 left-0 z-40 w-72 bg-slate-950 text-slate-100 p-6 transform transition-transform duration-200 lg:static lg:translate-x-0 lg:min-h-screen"
            :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
            <div class="flex items-center gap-3 mb-10">
                <div class="h-11 w-11 rounded-2xl bg-indigo-500/20 flex items-center justify-center text-indigo-300">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <div>
                    {{-- <div class="text-xs uppercase tracking-[0.3em] text-slate-400">Finance personnelles</div> --}}
                    <h1 class="font-semibold text-lg">MyFinance</h1>
                </div>
            </div>
            <nav class="space-y-2">
                <button v-for="tab in tabs" :key="tab.key" @click="activeTab = tab.key; mobileSidebarOpen = false"
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
            <header class="sticky top-0 z-20 bg-white border-b border-slate-200">
                <div class="px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <button v-if="auth.user" @click="mobileSidebarOpen = !mobileSidebarOpen" class="lg:hidden h-11 w-11 inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-700">
                            <i class="fa-solid fa-bars"></i>
                        </button>
                        <div>
                        <p class="text-sm text-slate-500">Finances personnelles</p>
                        <h2 class="text-xl font-semibold">@{{ title }}</h2>
                        </div>
                    </div>
                    <div v-if="auth.user" class="flex items-center gap-3">
                    <button @click="activeTab = 'notifications'; mobileSidebarOpen = false" class="relative inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50" aria-label="Notifications"><i class="fa-regular fa-bell"></i><span v-if="notificationUnreadCount" class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-bold text-white">@{{ notificationUnreadCount }}</span></button>
                    <button @click="logout" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 text-white px-4 py-2.5 text-sm font-medium hover:bg-slate-700">
                        <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
                    </button>
                    </div>
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
                        <div v-if="authError" class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                            @{{ authError }}
                        </div>

                        <form v-if="authMode==='login'" @submit.prevent="login" class="space-y-4">
                            <input v-model="loginForm.email" type="email" placeholder="Email" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                            <input v-model="loginForm.mot_de_passe" type="password" placeholder="Mot de passe" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                            <button :disabled="busyAuth" class="w-full rounded-2xl bg-indigo-600 text-white py-3 font-medium disabled:opacity-60">Se connecter</button>
                        </form>

                        <form v-else @submit.prevent="register" class="space-y-4">
                            <div class="grid sm:grid-cols-2 gap-4">
                                <input v-model="registerForm.nom" type="text" placeholder="Nom" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                                <input v-model="registerForm.prenom" type="text" placeholder="Prénom" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                            </div>
                            <input v-model="registerForm.email" type="email" placeholder="Email" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                            <input v-model="registerForm.mot_de_passe" type="password" placeholder="Mot de passe" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                            <input v-model="registerForm.mot_de_passe_confirmation" type="password" placeholder="Confirmation" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                            <button :disabled="busyAuth" class="w-full rounded-2xl bg-slate-900 text-white py-3 font-medium disabled:opacity-60">Créer le compte</button>
                        </form>
                    </div>
                </div>

                <div v-else class="space-y-8">
                    <div v-if="!['notifications', 'historiques', 'parametres'].includes(activeTab)" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="rounded-3xl bg-white border border-slate-200 p-5 shadow-sm"><div class="text-sm text-slate-500">Catégories</div><div class="mt-2 text-3xl font-semibold">@{{ categories.items.length }}</div></div>
                        <div class="rounded-3xl bg-white border border-slate-200 p-5 shadow-sm"><div class="text-sm text-slate-500">Revenus</div><div class="mt-2 text-3xl font-semibold">@{{ revenus.items.length }}</div></div>
                        <div class="rounded-3xl bg-white border border-slate-200 p-5 shadow-sm"><div class="text-sm text-slate-500">Dépenses</div><div class="mt-2 text-3xl font-semibold">@{{ depenses.items.length }}</div></div>
                        <div class="rounded-3xl bg-indigo-600 p-5 text-white shadow-lg shadow-indigo-600/20"><div class="text-sm text-indigo-100">Budgets</div><div class="mt-2 text-3xl font-semibold">@{{ budgets.stats.total }}</div><div class="mt-1 text-xs text-indigo-100">@{{ budgets.stats.actifs }} actif(s)</div></div>
                    </div>

                    <div v-if="activeTab==='categories'" class="grid lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-1 rounded-3xl bg-white border border-slate-200 p-6">
                            <h3 class="font-semibold text-lg mb-4">Catégorie</h3>
                            <form @submit.prevent="saveCategory" class="space-y-4">
                                <input v-model="categoryForm.nom_categorie" type="text" placeholder="Nom de catégorie" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                                <button type="submit" class="w-full rounded-2xl bg-slate-900 text-white py-3">Enregistrer</button>
                            </form>
                        </div>
                        <div class="lg:col-span-2 rounded-3xl bg-white border border-slate-200 p-6">
                            <div class="flex items-center justify-between gap-4 mb-4">
                                <input v-model="categories.search" @input="debouncedLoadCategories" placeholder="Rechercher" class="w-full max-w-sm rounded-2xl border border-slate-300 bg-white px-4 py-3 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
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
                            <input v-model="revenuForm.source" placeholder="Source" class="rounded-2xl border border-slate-300 bg-white px-4 py-3 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                            <input v-model="revenuForm.montant" type="number" step="0.01" placeholder="Montant" class="rounded-2xl border border-slate-300 bg-white px-4 py-3 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                            <input v-model="revenuForm.date_revenu" type="date" class="rounded-2xl border border-slate-300 bg-white px-4 py-3 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                        </div>
                        <textarea v-model="revenuForm.description" placeholder="Description" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"></textarea>
                        <div class="flex gap-3">
                            <button @click="saveRevenu" class="rounded-2xl bg-slate-900 text-white px-5 py-3">Enregistrer</button>
                            <input v-model="revenus.search" @input="debouncedLoadRevenus" placeholder="Rechercher" class="rounded-2xl border border-slate-300 bg-white px-4 py-3 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 flex-1">
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="text-slate-500"><tr><th class="text-left py-3">Source</th><th class="text-left py-3">Date</th><th class="text-left py-3">Montant</th><th></th></tr></thead>
                                <tbody>
                                    <tr v-for="item in revenus.items" :key="item.id_revenu" class="border-t">
                                        <td class="py-4">@{{ item.source }}</td><td>@{{ formatDate(item.date_revenu) }}</td><td>@{{ item.montant }}</td>
                                        <td class="text-right">
                                            <button @click="editRevenu(item)" class="text-indigo-600 mr-3">Modifier</button>
                                            <button @click="destroy('revenus', item.id_revenu)" class="text-rose-600">Supprimer</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div v-if="activeTab==='previsions-revenus'" class="space-y-6">
                        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                            <div><p class="text-sm font-medium text-emerald-600">Anticipation financière</p><h3 class="mt-1 text-2xl font-semibold tracking-tight text-slate-950">Prévisions de revenus</h3><p class="mt-2 text-sm text-slate-500">Planifiez vos entrées futures et suivez ce qui a déjà été perçu.</p></div>
                            <a href="{{ url('/revenu-previsions') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-emerald-200 bg-white px-4 py-3 text-sm font-semibold text-emerald-600 transition hover:bg-emerald-50 sm:w-auto"><i class="fa-solid fa-expand"></i>Vue détaillée</a>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            <div class="rounded-3xl border border-indigo-100 bg-indigo-50 p-5"><div class="text-sm font-medium text-indigo-600">Prévisions</div><div class="mt-2 text-3xl font-bold text-slate-950">@{{ revenuPrevisions.stats.total }}</div></div>
                            <div class="rounded-3xl border border-violet-100 bg-violet-50 p-5"><div class="text-sm font-medium text-violet-600">Montant prévu</div><div class="mt-2 text-2xl font-bold text-slate-950">@{{ formatMoney(revenuPrevisions.stats.montant_total) }} <span class="text-sm text-slate-500">FC</span></div></div>
                            <div class="rounded-3xl border border-amber-100 bg-amber-50 p-5"><div class="text-sm font-medium text-amber-600">Revenus attendus</div><div class="mt-2 text-3xl font-bold text-slate-950">@{{ revenuPrevisions.stats.attendus }}</div></div>
                            <div class="rounded-3xl border border-emerald-100 bg-emerald-50 p-5"><div class="text-sm font-medium text-emerald-600">Source principale</div><div class="mt-2 truncate text-xl font-bold text-slate-950">@{{ revenuPrevisions.stats.source_principale || 'Aucune' }}</div></div>
                        </div>
                        <div class="grid gap-6 lg:grid-cols-3">
                            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                                <h4 class="text-lg font-semibold text-slate-950">@{{ revenuPrevisionForm.id_revenu_prevision ? 'Modifier la prévision' : 'Nouvelle prévision' }}</h4><p class="mt-1 text-sm text-slate-500">Tous les champs sont obligatoires.</p>
                                <form @submit.prevent="saveRevenuPrevision" class="mt-5 space-y-4">
                                    <div><label for="revenu-prevision-source" class="mb-1.5 block text-sm font-medium text-slate-700">Source prévue</label><input id="revenu-prevision-source" v-model="revenuPrevisionForm.source_previsionnelle" type="text" placeholder="Salaire, bonus..." class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"><p v-if="revenuPrevisionErrors.source_previsionnelle" class="mt-1 text-xs font-medium text-rose-600">@{{ revenuPrevisionErrors.source_previsionnelle[0] }}</p></div>
                                    <div><label for="revenu-prevision-montant" class="mb-1.5 block text-sm font-medium text-slate-700">Montant prévu</label><input id="revenu-prevision-montant" v-model="revenuPrevisionForm.montant_previsionnel" type="number" min="0.01" step="0.01" placeholder="0,00" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"><p v-if="revenuPrevisionErrors.montant_previsionnel" class="mt-1 text-xs font-medium text-rose-600">@{{ revenuPrevisionErrors.montant_previsionnel[0] }}</p></div>
                                    <div><label for="revenu-prevision-date" class="mb-1.5 block text-sm font-medium text-slate-700">Date prévue</label><input id="revenu-prevision-date" v-model="revenuPrevisionForm.date_previsionnelle" type="date" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"><p v-if="revenuPrevisionErrors.date_previsionnelle" class="mt-1 text-xs font-medium text-rose-600">@{{ revenuPrevisionErrors.date_previsionnelle[0] }}</p></div>
                                    <div><label for="revenu-prevision-description" class="mb-1.5 block text-sm font-medium text-slate-700">Description</label><textarea id="revenu-prevision-description" v-model="revenuPrevisionForm.description" rows="3" placeholder="Décrire ce revenu..." class="w-full resize-none rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"></textarea><p v-if="revenuPrevisionErrors.description" class="mt-1 text-xs font-medium text-rose-600">@{{ revenuPrevisionErrors.description[0] }}</p></div>
                                    <div class="flex flex-col gap-3 sm:flex-row"><button type="submit" class="flex-1 rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">@{{ revenuPrevisionForm.id_revenu_prevision ? 'Mettre à jour' : 'Créer' }}</button><button v-if="revenuPrevisionForm.id_revenu_prevision" type="button" @click="resetRevenuPrevisionForm" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-500">Annuler</button></div>
                                </form>
                            </div>
                            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 lg:col-span-2">
                                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center"><div><h4 class="text-lg font-semibold text-slate-950">Revenus planifiés</h4><p class="mt-1 text-sm text-slate-500">@{{ revenuPrevisions.stats.prochaine_source ? 'Prochain : ' + revenuPrevisions.stats.prochaine_source : 'Aucun revenu à venir' }}</p></div><div class="flex flex-col gap-3 sm:flex-row"><input v-model="revenuPrevisions.search" @input="debouncedLoadRevenuPrevisions" placeholder="Rechercher" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 sm:w-44"><select v-model="revenuPrevisions.sort" @change="loadRevenuPrevisions" class="w-full rounded-2xl border border-slate-300 bg-white px-3 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 sm:w-auto"><option value="date_previsionnelle">Date</option><option value="montant_previsionnel">Montant</option><option value="source_previsionnelle">Source</option></select></div></div>
                                <div v-if="!revenuPrevisions.items.length" class="mt-5 rounded-2xl bg-slate-50 px-5 py-12 text-center text-sm text-slate-500"><i class="fa-solid fa-arrow-trend-up mb-3 text-2xl text-emerald-400"></i><p>Aucune prévision de revenu trouvée.</p></div>
                                <div v-else class="mt-5 space-y-3"><div v-for="item in revenuPrevisions.items" :key="'mobile-' + item.id_revenu_prevision" class="rounded-2xl border border-slate-100 p-4 md:hidden"><div class="flex items-start justify-between gap-3"><div class="min-w-0"><p class="truncate font-semibold text-slate-900">@{{ item.source_previsionnelle }}</p><p class="mt-1 text-sm text-slate-500">@{{ formatDate(item.date_previsionnelle) }}</p></div><span class="shrink-0 inline-flex rounded-full px-2.5 py-1 text-xs font-semibold" :class="revenuPrevisionStatusClass(item.statut)">@{{ item.statut }}</span></div><p class="mt-3 font-semibold text-slate-700">@{{ formatMoney(item.montant_previsionnel) }} <span class="text-xs text-slate-400">FC</span></p><div class="mt-4 flex flex-wrap gap-x-4 gap-y-2 text-sm"><button v-if="item.statut !== 'Réalisée'" @click="markRevenuPrevision(item)" class="font-semibold text-emerald-600">Perçu</button><button @click="editRevenuPrevision(item)" class="text-indigo-600">Modifier</button><button @click="destroy('revenu-previsions', item.id_revenu_prevision)" class="text-rose-600">Supprimer</button></div></div><div class="hidden overflow-x-auto md:block"><table class="w-full min-w-[760px] text-sm"><thead class="border-b border-slate-100 text-left text-xs uppercase tracking-wider text-slate-400"><tr><th class="py-3">Source</th><th class="py-3">Montant</th><th class="py-3">Date</th><th class="py-3">Statut</th><th></th></tr></thead><tbody><tr v-for="item in revenuPrevisions.items" :key="item.id_revenu_prevision" class="border-b border-slate-100 last:border-0"><td class="py-4 font-semibold text-slate-900">@{{ item.source_previsionnelle }}</td><td class="py-4 font-semibold text-slate-700">@{{ formatMoney(item.montant_previsionnel) }} <span class="text-xs text-slate-400">FC</span></td><td class="py-4 text-slate-500">@{{ formatDate(item.date_previsionnelle) }}</td><td class="py-4"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold" :class="revenuPrevisionStatusClass(item.statut)">@{{ item.statut }}</span></td><td class="py-4 text-right whitespace-nowrap"><button v-if="item.statut !== 'Réalisée'" @click="markRevenuPrevision(item)" class="mr-3 font-semibold text-emerald-600">Perçu</button><button @click="editRevenuPrevision(item)" class="mr-3 text-indigo-600">Modifier</button><button @click="destroy('revenu-previsions', item.id_revenu_prevision)" class="text-rose-600">Supprimer</button></td></tr></tbody></table></div></div>
                            </div>
                        </div>
                    </div>

                    <div v-if="activeTab==='depenses'" class="rounded-3xl bg-white border border-slate-200 p-6 space-y-6">
                        <div class="grid lg:grid-cols-4 gap-4">
                            <select v-model="depenseForm.id_categorie" class="rounded-2xl border border-slate-300 bg-white px-4 py-3 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                                <option value="">Catégorie</option>
                                <option v-for="item in categories.items" :key="item.id_categorie" :value="item.id_categorie">@{{ item.nom_categorie }}</option>
                            </select>
                            <input v-model="depenseForm.montant" type="number" step="0.01" placeholder="Montant" class="rounded-2xl border border-slate-300 bg-white px-4 py-3 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                            <input v-model="depenseForm.date_depense" type="date" class="rounded-2xl border border-slate-300 bg-white px-4 py-3 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                            <button @click="saveDepense" class="rounded-2xl bg-slate-900 text-white px-5 py-3">Enregistrer</button>
                        </div>
                        <textarea v-model="depenseForm.description" placeholder="Description" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"></textarea>
                        <div class="flex gap-3">
                            <input v-model="depenses.search" @input="debouncedLoadDepenses" placeholder="Rechercher" class="rounded-2xl border border-slate-300 bg-white px-4 py-3 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 flex-1">
                            <select v-model="depenses.sort" @change="loadDepenses" class="rounded-2xl border border-slate-300 bg-white px-4 py-3 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                                <option value="date_depense">Date</option><option value="id_depense">Récent</option>
                            </select>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="text-slate-500"><tr><th class="text-left py-3">Catégorie</th><th class="text-left py-3">Achat</th><th class="text-left py-3">Date</th><th class="text-left py-3">Montant</th><th></th></tr></thead>
                                <tbody>
                                    <tr v-for="item in depenses.items" :key="item.id_depense" class="border-t">
                                        <td class="py-4">@{{ item.categorie ? item.categorie.nom_categorie : '-' }}</td><td class="max-w-xs truncate py-4 font-medium text-slate-700" :title="item.description || 'Aucun achat renseigné'">@{{ item.description || '-' }}</td><td>@{{ formatDate(item.date_depense) }}</td><td>@{{ item.montant }}</td>
                                        <td class="text-right">
                                            <button @click="editDepense(item)" class="text-indigo-600 mr-3">Modifier</button>
                                            <button @click="destroy('depenses', item.id_depense)" class="text-rose-600">Supprimer</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div v-if="activeTab==='previsions'" class="space-y-6">
                        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                            <div><p class="text-sm font-medium text-indigo-600">Anticipation financière</p><h3 class="mt-1 text-2xl font-semibold tracking-tight text-slate-950">Prévisions de dépenses</h3><p class="mt-2 text-sm text-slate-500">Planifiez vos dépenses futures et anticipez vos besoins.</p></div>
                            <a href="{{ url('/depense-previsions') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-indigo-200 bg-white px-4 py-3 text-sm font-semibold text-indigo-600 transition hover:bg-indigo-50 sm:w-auto"><i class="fa-solid fa-expand"></i>Vue détaillée</a>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            <div class="rounded-3xl border border-indigo-100 bg-indigo-50 p-5"><div class="text-sm font-medium text-indigo-600">Prévisions</div><div class="mt-2 text-3xl font-bold text-slate-950">@{{ previsions.stats.total }}</div></div>
                            <div class="rounded-3xl border border-violet-100 bg-violet-50 p-5"><div class="text-sm font-medium text-violet-600">Montant total</div><div class="mt-2 text-2xl font-bold text-slate-950">@{{ formatMoney(previsions.stats.montant_total) }} <span class="text-sm text-slate-500">FC</span></div></div>
                            <div class="rounded-3xl border border-amber-100 bg-amber-50 p-5"><div class="text-sm font-medium text-amber-600">En attente</div><div class="mt-2 text-3xl font-bold text-slate-950">@{{ previsions.stats.en_attente }}</div></div>
                            <div class="rounded-3xl border border-rose-100 bg-rose-50 p-5"><div class="text-sm font-medium text-rose-600">Dépassées</div><div class="mt-2 text-3xl font-bold text-slate-950">@{{ previsions.stats.depassees }}</div></div>
                        </div>
                        <div class="grid gap-6 lg:grid-cols-3">
                            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                                <h4 class="text-lg font-semibold text-slate-950">@{{ previsionForm.id_depense_prevision ? 'Modifier la prévision' : 'Nouvelle prévision' }}</h4><p class="mt-1 text-sm text-slate-500">Une description est obligatoire.</p>
                                <form @submit.prevent="savePrevision" class="mt-5 space-y-4">
                                    <div><label for="prevision-categorie" class="mb-1.5 block text-sm font-medium text-slate-700">Catégorie</label><select id="prevision-categorie" v-model="previsionForm.id_categorie" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"><option value="">Sélectionner</option><option v-for="item in categories.items" :key="item.id_categorie" :value="item.id_categorie">@{{ item.nom_categorie }}</option></select><p v-if="previsionErrors.id_categorie" class="mt-1 text-xs font-medium text-rose-600">@{{ previsionErrors.id_categorie[0] }}</p></div>
                                    <div><label for="prevision-montant" class="mb-1.5 block text-sm font-medium text-slate-700">Montant prévu</label><input id="prevision-montant" v-model="previsionForm.montant_previsionnel" type="number" min="0.01" step="0.01" placeholder="0,00" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"><p v-if="previsionErrors.montant_previsionnel" class="mt-1 text-xs font-medium text-rose-600">@{{ previsionErrors.montant_previsionnel[0] }}</p></div>
                                    <div><label for="prevision-date" class="mb-1.5 block text-sm font-medium text-slate-700">Date prévue</label><input id="prevision-date" v-model="previsionForm.date_previsionnelle" type="date" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"><p v-if="previsionErrors.date_previsionnelle" class="mt-1 text-xs font-medium text-rose-600">@{{ previsionErrors.date_previsionnelle[0] }}</p></div>
                                    <div><label for="prevision-description" class="mb-1.5 block text-sm font-medium text-slate-700">Description</label><textarea id="prevision-description" v-model="previsionForm.description" rows="3" placeholder="Décrire la dépense..." class="w-full resize-none rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"></textarea><p v-if="previsionErrors.description" class="mt-1 text-xs font-medium text-rose-600">@{{ previsionErrors.description[0] }}</p></div>
                                    <div class="flex flex-col gap-3 sm:flex-row"><button type="submit" class="flex-1 rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700">@{{ previsionForm.id_depense_prevision ? 'Mettre à jour' : 'Créer' }}</button><button v-if="previsionForm.id_depense_prevision" type="button" @click="resetPrevisionForm" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-500">Annuler</button></div>
                                </form>
                            </div>
                            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 lg:col-span-2">
                                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center"><div><h4 class="text-lg font-semibold text-slate-950">Dépenses planifiées</h4><p class="mt-1 text-sm text-slate-500">@{{ previsions.stats.categorie_frequente ? 'Catégorie principale : ' + previsions.stats.categorie_frequente : 'Aucune catégorie principale' }}</p></div><div class="flex flex-col gap-3 sm:flex-row"><input v-model="previsions.search" @input="debouncedLoadPrevisions" placeholder="Rechercher" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 sm:w-44"><select v-model="previsions.sort" @change="loadPrevisions" class="w-full rounded-2xl border border-slate-300 bg-white px-3 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 sm:w-auto"><option value="date_previsionnelle">Date</option><option value="montant_previsionnel">Montant</option><option value="id_categorie">Catégorie</option></select></div></div>
                                <div v-if="!previsions.items.length" class="mt-5 rounded-2xl bg-slate-50 px-5 py-12 text-center text-sm text-slate-500"><i class="fa-solid fa-calendar-plus mb-3 text-2xl text-indigo-400"></i><p>Aucune prévision trouvée.</p></div>
                                <div v-else class="mt-5 space-y-3"><div v-for="item in previsions.items" :key="'mobile-' + item.id_depense_prevision" class="rounded-2xl border border-slate-100 p-4 md:hidden"><div class="flex items-start justify-between gap-3"><div class="min-w-0"><p class="truncate font-semibold text-slate-900">@{{ item.categorie ? item.categorie.nom_categorie : '-' }}</p><p class="mt-1 text-sm text-slate-500">@{{ formatDate(item.date_previsionnelle) }}</p></div><span class="shrink-0 inline-flex rounded-full px-2.5 py-1 text-xs font-semibold" :class="previsionStatusClass(item.statut)">@{{ item.statut }}</span></div><p class="mt-3 font-semibold text-slate-700">@{{ formatMoney(item.montant_previsionnel) }} <span class="text-xs text-slate-400">FC</span></p><div class="mt-4 flex flex-wrap gap-x-4 gap-y-2 text-sm"><button @click="validatePrevision(item)" class="font-semibold text-emerald-600">Valider</button><button @click="editPrevision(item)" class="text-indigo-600">Modifier</button><button @click="destroy('depense-previsions', item.id_depense_prevision)" class="text-rose-600">Supprimer</button></div></div><div class="hidden overflow-x-auto md:block"><table class="w-full min-w-[720px] text-sm"><thead class="border-b border-slate-100 text-left text-xs uppercase tracking-wider text-slate-400"><tr><th class="py-3">Catégorie</th><th class="py-3">Montant</th><th class="py-3">Date</th><th class="py-3">Statut</th><th></th></tr></thead><tbody><tr v-for="item in previsions.items" :key="item.id_depense_prevision" class="border-b border-slate-100 last:border-0"><td class="py-4 font-semibold text-slate-900">@{{ item.categorie ? item.categorie.nom_categorie : '-' }}</td><td class="py-4 font-semibold text-slate-700">@{{ formatMoney(item.montant_previsionnel) }} <span class="text-xs text-slate-400">FC</span></td><td class="py-4 text-slate-500">@{{ formatDate(item.date_previsionnelle) }}</td><td class="py-4"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold" :class="previsionStatusClass(item.statut)">@{{ item.statut }}</span></td><td class="py-4 text-right whitespace-nowrap"><button @click="validatePrevision(item)" class="mr-3 font-semibold text-emerald-600">Valider</button><button @click="editPrevision(item)" class="mr-3 text-indigo-600">Modifier</button><button @click="destroy('depense-previsions', item.id_depense_prevision)" class="text-rose-600">Supprimer</button></td></tr></tbody></table></div></div>
                            </div>
                        </div>
                    </div>

                    <div v-if="activeTab==='historiques'" class="space-y-6">
                        <div><p class="text-sm font-medium text-indigo-600">Analyse financière</p><h3 class="mt-1 text-2xl font-semibold tracking-tight text-slate-950">Historique des budgets</h3><p class="mt-2 text-sm text-slate-500">Retrouvez les revenus et dépenses de chaque cycle terminé.</p></div>
                        <div v-if="!historiques.items.length" class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center text-sm text-slate-500"><i class="fa-solid fa-clock-rotate-left mb-3 text-2xl text-indigo-400"></i><p>Aucun budget archivé pour le moment.</p></div>
                        <article v-for="historique in historiques.items" :key="historique.id_budget_historique" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="flex flex-wrap items-start justify-between gap-3"><div><h4 class="text-lg font-semibold text-slate-950">@{{ historique.periode }}</h4><p class="mt-1 text-sm text-slate-500">Archivé le @{{ formatDate(historique.date_archivage) }}</p></div><span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">Terminé</span></div>
                            <div class="mt-5 grid gap-3 sm:grid-cols-3"><div class="rounded-2xl bg-indigo-50 p-4"><p class="text-xs text-indigo-600">Montant initial</p><p class="mt-1 font-bold">@{{ formatMoney(historique.montant_total) }} FC</p></div><div class="rounded-2xl bg-rose-50 p-4"><p class="text-xs text-rose-600">Dépensé</p><p class="mt-1 font-bold">@{{ formatMoney(historique.montant_depense) }} FC</p></div><div class="rounded-2xl bg-emerald-50 p-4"><p class="text-xs text-emerald-600">Solde final</p><p class="mt-1 font-bold">@{{ formatMoney(historique.solde_final) }} FC</p></div></div>
                            <div class="mt-6 grid gap-5 lg:grid-cols-2"><div><h5 class="font-semibold text-slate-900">Dépenses (@{{ historique.depenses.length }})</h5><ul class="mt-2 space-y-2"><li v-for="depense in historique.depenses" :key="depense.id_depense" class="rounded-xl bg-rose-50 px-3 py-2 text-sm text-slate-700">@{{ depense.description || 'Dépense' }} — @{{ formatMoney(depense.montant) }} FC</li><li v-if="!historique.depenses.length" class="text-sm text-slate-500">Aucune dépense.</li></ul></div><div><h5 class="font-semibold text-slate-900">Revenus (@{{ historique.revenus.length }})</h5><ul class="mt-2 space-y-2"><li v-for="revenu in historique.revenus" :key="revenu.id_revenu" class="rounded-xl bg-emerald-50 px-3 py-2 text-sm text-slate-700">@{{ revenu.source }} — @{{ formatMoney(revenu.montant) }} FC</li><li v-if="!historique.revenus.length" class="text-sm text-slate-500">Aucun revenu.</li></ul></div></div>
                        </article>
                    </div>

                    <div v-if="activeTab==='notifications'" class="mx-auto max-w-4xl space-y-6">
                        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                            <div><p class="text-sm font-medium text-indigo-600">Centre de notifications</p><h3 class="mt-1 text-2xl font-semibold tracking-tight text-slate-950">Restez informé</h3></div>
                            <div v-if="notifications.items.length" class="flex flex-col gap-3 sm:flex-row"><button @click="markAllNotificationsAsRead" class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 sm:w-auto">Tout lire</button><button @click="deleteAllNotifications" class="w-full rounded-2xl bg-rose-50 px-4 py-2.5 text-sm font-semibold text-rose-600 hover:bg-rose-100 sm:w-auto">Tout supprimer</button></div>
                        </div>
                        <div v-if="!notifications.items.length" class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center"><div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-indigo-50 text-2xl text-indigo-500"><i class="fa-regular fa-bell"></i></div><h4 class="mt-5 text-xl font-bold text-slate-950">Aucune notification</h4><p class="mt-2 text-sm text-slate-500">Les événements importants de votre gestion financière apparaîtront ici.</p></div>
                        <article v-for="notification in notifications.items" :key="notification.id_notification" class="flex gap-4 rounded-3xl border p-5 shadow-sm" :class="notification.est_lue ? 'border-slate-200 bg-white opacity-75' : 'border-indigo-300 bg-indigo-50 ring-1 ring-indigo-100'">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl" :class="notification.type.includes('depense') ? 'bg-rose-100 text-rose-600' : 'bg-emerald-100 text-emerald-600'"><i class="fa-solid" :class="notification.type.includes('depense') ? 'fa-receipt' : 'fa-circle-check'"></i></div>
                            <div class="min-w-0 flex-1"><div class="flex flex-wrap items-start justify-between gap-2"><p class="font-bold text-slate-950">@{{ notification.titre }}</p><time class="text-xs text-slate-400">@{{ formatDateTime(notification.date_notification) }}</time></div><p class="mt-1 text-sm leading-6 text-slate-600">@{{ notification.contenu }}</p><div class="mt-3 flex flex-wrap items-center gap-3"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold" :class="notification.est_lue ? 'bg-slate-100 text-slate-500' : 'bg-indigo-100 text-indigo-700'">@{{ notification.est_lue ? 'Lue' : 'Non lue' }}</span><button v-if="!notification.est_lue" @click="markNotificationAsRead(notification)" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">Marquer comme lue</button></div></div>
                            <button @click="deleteNotification(notification)" class="text-slate-400 hover:text-rose-600" aria-label="Supprimer la notification"><i class="fa-solid fa-trash"></i></button>
                        </article>
                    </div>

                    <div v-if="activeTab==='parametres'" class="mx-auto max-w-2xl space-y-6">
                        <div><p class="text-sm font-medium text-indigo-600">Configuration</p><h3 class="mt-1 text-2xl font-semibold tracking-tight text-slate-950">Paramètres de notification</h3><p class="mt-2 text-sm text-slate-500">Configurez le comportement des alertes pour rester informé à votre manière.</p></div>

                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-6">
                            <div class="flex items-center justify-between gap-4">
                                <div><p class="font-semibold text-slate-900">Son des notifications</p><p class="mt-0.5 text-sm text-slate-500">Jouer un son lors de l'arrivée d'une nouvelle notification.</p></div>
                                <label class="relative inline-flex cursor-pointer items-center"><input type="checkbox" v-model="notificationPreferences.notif_son" class="peer sr-only"><div class="h-6 w-11 rounded-full bg-slate-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow-sm after:transition-all peer-checked:bg-indigo-600 peer-checked:after:translate-x-full"></div></label>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <div><p class="font-semibold text-slate-900">Vibration</p><p class="mt-0.5 text-sm text-slate-500">Faire vibrer l'appareil (mobile uniquement).</p></div>
                                <label class="relative inline-flex cursor-pointer items-center"><input type="checkbox" v-model="notificationPreferences.notif_vibration" class="peer sr-only"><div class="h-6 w-11 rounded-full bg-slate-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow-sm after:transition-all peer-checked:bg-indigo-600 peer-checked:after:translate-x-full"></div></label>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <div><p class="font-semibold text-slate-900">Notifications du navigateur</p><p class="mt-0.5 text-sm text-slate-500">Afficher une notification native du système d'exploitation.</p></div>
                                <label class="relative inline-flex cursor-pointer items-center"><input type="checkbox" v-model="notificationPreferences.notif_navigateur" @change="requestBrowserNotificationPermission()" class="peer sr-only"><div class="h-6 w-11 rounded-full bg-slate-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow-sm after:transition-all peer-checked:bg-indigo-600 peer-checked:after:translate-x-full"></div></label>
                            </div>
                            <div v-if="browserPermission === 'denied'" class="rounded-2xl bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-700">
                                <i class="fa-solid fa-triangle-exclamation mr-1"></i> Les notifications sont bloquées par votre navigateur. Modifiez les paramètres du site dans votre navigateur.
                            </div>
                        </div>

                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h4 class="font-semibold text-slate-900">Tester les notifications</h4>
                            <p class="mt-1 text-sm text-slate-500">Cliquez pour prévisualiser le son et la vibration.</p>
                            <button @click="testNotificationSound" class="mt-4 inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-700 transition"><i class="fa-solid fa-volume-high"></i> Tester le son</button>
                        </div>

                        <div class="flex justify-end">
                            <button @click="saveNotificationPreferences" class="inline-flex items-center gap-2 rounded-2xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white hover:bg-indigo-700 transition"><i class="fa-solid fa-check"></i> Enregistrer</button>
                        </div>
                    </div>

                    <div v-if="activeTab==='budgets'" class="mx-auto max-w-7xl space-y-6">
                        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                            <div>
                                <p class="text-sm font-medium text-indigo-600">Planification financière</p>
                                <h3 class="mt-1 text-2xl font-semibold tracking-tight text-slate-950">Mes budgets</h3>
                                <p class="mt-2 text-sm text-slate-500">Créez une enveloppe, suivez sa période et gardez le contrôle de vos objectifs.</p>
                            </div>
                            <a href="{{ url('/budgets') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-indigo-200 bg-white px-4 py-3 text-sm font-semibold text-indigo-600 transition hover:bg-indigo-50 sm:w-auto">
                                <i class="fa-solid fa-expand"></i>
                                Vue détaillée
                            </a>
                        </div>

                        <div class="grid grid-cols-1 gap-4 min-[420px]:grid-cols-2 lg:grid-cols-5">
                            <div class="rounded-3xl border border-indigo-100 bg-indigo-50 p-5"><div class="text-sm font-medium text-indigo-600">Total budgets</div><div class="mt-2 text-3xl font-bold text-slate-950">@{{ budgets.stats.total }}</div></div>
                            <div class="rounded-3xl border border-emerald-100 bg-emerald-50 p-5"><div class="text-sm font-medium text-emerald-600">Budget actif</div><div class="mt-2 text-3xl font-bold text-slate-950">@{{ budgets.stats.actifs }}</div></div>
                            <div class="rounded-3xl border border-violet-100 bg-violet-50 p-5"><div class="text-sm font-medium text-violet-600">Budget initial</div><div class="mt-2 text-2xl font-bold text-slate-950">@{{ formatMoney(budgets.stats.montant_initial) }} <span class="text-sm font-semibold text-slate-500">FC</span></div></div>
                            <div class="rounded-3xl border border-rose-100 bg-rose-50 p-5"><div class="text-sm font-medium text-rose-600">Dépensé</div><div class="mt-2 text-2xl font-bold text-slate-950">@{{ formatMoney(budgets.stats.montant_depense) }} <span class="text-sm font-semibold text-slate-500">FC</span></div></div>
                            <div class="rounded-3xl border border-teal-100 bg-teal-50 p-5"><div class="text-sm font-medium text-teal-600">Budget restant</div><div class="mt-2 text-2xl font-bold text-slate-950">@{{ formatMoney(budgets.stats.montant_restant) }} <span class="text-sm font-semibold text-slate-500">FC</span></div></div>
                        </div>

                        <div class="grid gap-6 lg:grid-cols-3">
                            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                                <div class="mb-5">
                                    <h4 class="text-lg font-semibold text-slate-950">@{{ budgetForm.id_budget ? 'Modifier le budget' : 'Nouveau budget' }}</h4>
                                    <p class="mt-1 text-sm text-slate-500">Les champs sont contrôlés avant enregistrement.</p>
                                </div>
                                <form @submit.prevent="saveBudget" class="space-y-4">
                                    <div>
                                        <label for="budget-periode" class="mb-1.5 block text-sm font-medium text-slate-700">Période</label>
                                        <input id="budget-periode" v-model="budgetForm.periode" type="text" placeholder="Ex. Août 2026" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                                        <p v-if="budgetErrors.periode" class="mt-1 text-xs font-medium text-rose-600">@{{ budgetErrors.periode[0] }}</p>
                                    </div>
                                    <div>
                                        <label for="budget-montant" class="mb-1.5 block text-sm font-medium text-slate-700">Montant total</label>
                                        <input id="budget-montant" v-model="budgetForm.montant_total" type="number" min="0.01" step="0.01" placeholder="0,00" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                                        <p v-if="budgetErrors.montant_total" class="mt-1 text-xs font-medium text-rose-600">@{{ budgetErrors.montant_total[0] }}</p>
                                    </div>
                                    <div class="grid gap-3 min-[420px]:grid-cols-2">
                                        <div>
                                            <label for="budget-debut" class="mb-1.5 block text-sm font-medium text-slate-700">Début</label>
                                            <input id="budget-debut" v-model="budgetForm.date_debut" type="date" class="w-full rounded-2xl border border-slate-300 bg-white px-3 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                                            <p v-if="budgetErrors.date_debut" class="mt-1 text-xs font-medium text-rose-600">@{{ budgetErrors.date_debut[0] }}</p>
                                        </div>
                                        <div>
                                            <label for="budget-fin" class="mb-1.5 block text-sm font-medium text-slate-700">Fin</label>
                                            <input id="budget-fin" v-model="budgetForm.date_fin" type="date" :min="budgetForm.date_debut || null" class="w-full rounded-2xl border border-slate-300 bg-white px-3 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                                            <p v-if="budgetErrors.date_fin" class="mt-1 text-xs font-medium text-rose-600">@{{ budgetErrors.date_fin[0] }}</p>
                                        </div>
                                    </div>
                                    <div class="flex flex-col gap-3 pt-2 min-[420px]:flex-row">
                                        <button type="submit" class="w-full flex-1 rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700">@{{ budgetForm.id_budget ? 'Mettre à jour' : 'Créer le budget' }}</button>
                                        <button v-if="budgetForm.id_budget" type="button" @click="resetBudgetForm" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-500 transition hover:bg-slate-50 min-[420px]:w-auto">Annuler</button>
                                    </div>
                                    <label v-if="budgetForm.id_budget" class="flex cursor-pointer items-start gap-3 rounded-2xl border border-indigo-100 bg-indigo-50/60 p-4 text-sm text-slate-700">
                                        <input v-model="budgetForm.reinitialiser_solde" type="checkbox" class="mt-0.5 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                        <span><span class="block font-semibold text-slate-900">Réinitialiser le solde</span><span class="mt-0.5 block text-slate-500">Utiliser le nouveau montant comme solde restant.</span></span>
                                    </label>
                                </form>
                            </div>

                            <div class="min-w-0 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 lg:col-span-2">
                                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                                    <div>
                                        <h4 class="text-lg font-semibold text-slate-950">Budgets enregistrés</h4>
                                        <p class="mt-1 text-sm text-slate-500">@{{ budgets.stats.total }} budget(s) au total</p>
                                    </div>
                                    <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row">
                                        <input v-model="budgets.search" @input="debouncedLoadBudgets" placeholder="Rechercher" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 sm:w-44">
                                        <select v-model="budgets.sort" @change="loadBudgets" class="w-full rounded-2xl border border-slate-300 bg-white px-3 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 sm:w-auto">
                                            <option value="date_debut">Date</option><option value="periode">Période</option><option value="montant_total">Montant</option>
                                        </select>
                                    </div>
                                </div>

                                <div v-if="!budgets.items.length" class="rounded-2xl bg-slate-50 px-5 py-12 text-center text-sm text-slate-500">
                                    <i class="fa-solid fa-chart-pie mb-3 text-2xl text-indigo-400"></i>
                                    <p>Aucun budget ne correspond à votre recherche.</p>
                                </div>

                                <div v-else class="mt-5 space-y-3 md:hidden">
                                    <article v-for="item in budgets.items" :key="`mobile-${item.id_budget_historique || item.id_budget}`" class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                                        <div class="flex items-start justify-between gap-3"><div class="min-w-0"><p class="truncate font-semibold text-slate-900">@{{ item.periode }}</p><p class="mt-1 text-sm font-semibold text-slate-700">@{{ formatMoney(item.montant_total) }} FC</p></div><span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold" :class="statusClass(item.statut)">@{{ item.statut }}</span></div>
                                        <p class="mt-3 text-sm text-slate-500">@{{ formatDate(item.date_debut) }} → @{{ formatDate(item.date_fin) }}</p>
                                        <div v-if="!item.est_historique" class="mt-4 flex gap-4 text-sm font-semibold"><button @click="editBudget(item)" class="text-indigo-600">Modifier</button><button @click="destroy('budgets', item.id_budget)" class="text-rose-600">Supprimer</button></div>
                                    </article>
                                </div>

                                <div v-if="budgets.items.length" class="mt-5 hidden overflow-x-auto md:block">
                                    <table class="w-full min-w-[680px] text-sm">
                                        <thead class="border-b border-slate-100 text-left text-xs uppercase tracking-wider text-slate-400"><tr><th class="py-3">Période</th><th class="py-3">Montant</th><th class="py-3">Dates</th><th class="py-3">Statut</th><th></th></tr></thead>
                                        <tbody>
                                            <tr v-for="item in budgets.items" :key="item.id_budget_historique || item.id_budget" class="border-b border-slate-100 last:border-0">
                                                <td class="py-4 font-semibold text-slate-900">@{{ item.periode }}</td>
                                                <td class="py-4 font-semibold text-slate-700">@{{ formatMoney(item.montant_total) }} <span class="text-xs text-slate-400">FC</span></td>
                                                <td class="py-4 text-slate-500">@{{ formatDate(item.date_debut) }} → @{{ formatDate(item.date_fin) }}</td>
                                                <td class="py-4"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold" :class="statusClass(item.statut)">@{{ item.statut }}</span></td>
                                                <td class="py-4 text-right"><template v-if="!item.est_historique"><button @click="editBudget(item)" class="mr-3 text-indigo-600">Modifier</button><button @click="destroy('budgets', item.id_budget)" class="text-rose-600">Supprimer</button></template><span v-else class="text-xs font-semibold text-slate-400">Consultation</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.prod.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;
const api = axios.create({
    baseURL: '/api',
    withCredentials: true,
    headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
    },
});
api.interceptors.request.use((config) => {
    if (!config.silentLoading) window.dispatchEvent(new Event('finance-loading-start'));
    return config;
});
api.interceptors.response.use(
    (response) => {
        if (!response.config.silentLoading) window.dispatchEvent(new Event('finance-loading-end'));
        return response;
    },
    (error) => {
        if (!error.config?.silentLoading) window.dispatchEvent(new Event('finance-loading-end'));
        return Promise.reject(error);
    },
);
const debounce = (fn, wait = 300) => { let t; return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), wait); }; };

Vue.createApp({
    data() {
        return {
            activeTab: 'categories',
            authMode: 'login',
            auth: { user: null },
            mobileSidebarOpen: false,
            isLoading: false,
            loadingProgress: 0,
            loadingTimer: null,
            busyAuth: false,
            authError: '',
            notificationUnreadCount: 0,
            tabs: [
                { key: 'categories', label: 'Catégories', icon: 'fa-solid fa-tags' },
                { key: 'revenus', label: 'Revenus', icon: 'fa-solid fa-coins' },
                { key: 'previsions-revenus', label: 'Prévisions de revenus', icon: 'fa-solid fa-arrow-trend-up' },
                { key: 'depenses', label: 'Dépenses', icon: 'fa-solid fa-receipt' },
                { key: 'budgets', label: 'Budgets', icon: 'fa-solid fa-chart-pie' },
                { key: 'historiques', label: 'Historique', icon: 'fa-solid fa-clock-rotate-left' },
                { key: 'previsions', label: 'Prévisions de dépenses', icon: 'fa-solid fa-calendar-days' },
                { key: 'notifications', label: 'Notifications', icon: 'fa-regular fa-bell' },
                { key: 'parametres', label: 'Paramètres', icon: 'fa-solid fa-gear' },
            ],
            loginForm: { email: '', mot_de_passe: '' },
            registerForm: { nom: '', prenom: '', email: '', mot_de_passe: '', mot_de_passe_confirmation: '' },
            categoryForm: { id_categorie: null, nom_categorie: '' },
            revenuForm: { id_revenu: null, source: '', montant: '', date_revenu: '', description: '' },
            revenuPrevisionForm: { id_revenu_prevision: null, montant_previsionnel: '', source_previsionnelle: '', date_previsionnelle: '', description: '' },
            revenuPrevisionErrors: {},
            depenseForm: { id_depense: null, id_categorie: '', montant: '', date_depense: '', description: '' },
            budgetForm: { id_budget: null, periode: '', montant_total: '', date_debut: '', date_fin: '' },
            budgetErrors: {},
            previsionForm: { id_depense_prevision: null, id_categorie: '', montant_previsionnel: '', date_previsionnelle: '', description: '' },
            previsionErrors: {},
            categories: { items: [], search: '' },
            revenus: { items: [], search: '', sort: 'date_revenu', direction: 'desc' },
            revenuPrevisions: { items: [], search: '', sort: 'date_previsionnelle', direction: 'asc', stats: { total: 0, montant_total: 0, montant_mois: 0, montant_annee: 0, attendus: 0, expirees: 0, prochaine_date: null, prochaine_source: null, source_principale: null } },
            depenses: { items: [], search: '', sort: 'date_depense', direction: 'desc' },
            budgets: { items: [], search: '', sort: 'date_debut', direction: 'desc', stats: { total: 0, actifs: 0, montant_total: 0, montant_initial: 0, montant_depense: 0, montant_restant: 0 } },
            historiques: { items: [] },
            notifications: { items: [], knownIds: [], refreshTimer: null, initialized: false },
            notificationPreferences: { notif_son: true, notif_vibration: true, notif_navigateur: false },
            notifAudio: null,
            browserPermission: 'default',
            previsions: { items: [], search: '', sort: 'date_previsionnelle', direction: 'asc', stats: { total: 0, montant_total: 0, en_attente: 0, depassees: 0, prochaine_date: null, prochaine_categorie: null, categorie_frequente: null } },
            debouncedLoadCategories: null,
            debouncedLoadRevenus: null,
            debouncedLoadRevenuPrevisions: null,
            debouncedLoadDepenses: null,
            debouncedLoadBudgets: null,
            debouncedLoadPrevisions: null,
        };
    },
    computed: {
        title() {
            return this.tabs.find(tab => tab.key === this.activeTab)?.label ?? 'Accueil';
        }
    },
    mounted() {
        window.addEventListener('finance-loading-start', this.startLoading);
        window.addEventListener('finance-loading-end', this.finishLoading);
        this.debouncedLoadCategories = debounce(() => this.loadCategories(), 250);
        this.debouncedLoadRevenus = debounce(() => this.loadRevenus(), 250);
        this.debouncedLoadRevenuPrevisions = debounce(() => this.loadRevenuPrevisions(), 250);
        this.debouncedLoadDepenses = debounce(() => this.loadDepenses(), 250);
        this.debouncedLoadBudgets = debounce(() => this.loadBudgets(), 250);
        this.debouncedLoadPrevisions = debounce(() => this.loadPrevisions(), 250);
        this.bootstrap();
        this.notifications.refreshTimer = window.setInterval(() => this.loadNotifications(), 15000);
        this.notifAudio = new Audio('/sounds/notification.wav');
        if ('Notification' in window) this.browserPermission = Notification.permission;
    },
    beforeUnmount() { window.clearInterval(this.notifications.refreshTimer); },
    methods: {
        startLoading() {
            this.isLoading = true;
            this.loadingProgress = Math.max(this.loadingProgress, 20);
            window.clearTimeout(this.loadingTimer);
            this.loadingTimer = window.setTimeout(() => {
                if (this.isLoading) this.loadingProgress = Math.max(this.loadingProgress, 75);
            }, 180);
        },
        finishLoading() {
            this.loadingProgress = 100;
            window.clearTimeout(this.loadingTimer);
            this.loadingTimer = window.setTimeout(() => {
                this.isLoading = false;
                this.loadingProgress = 0;
            }, 350);
        },
        async bootstrap() {
            this.isLoading = true;
            this.loadingProgress = 15;
            try {
                const { data } = await api.get('/auth/me');
                this.auth.user = data.data;
                this.loadingProgress = 45;
                await this.loadAll();
                await this.loadNotificationPreferences();
                this.loadingProgress = 100;
            } catch (error) {
                if (error?.response?.status !== 401) {
                    toastr.error(this.errorMessage(error, 'Impossible de contacter le serveur.'));
                }
            } finally {
                this.loadingProgress = 100;
                this.isLoading = false;
            }
        },
        async loadAll() {
            await this.loadCategories();
            await Promise.allSettled([this.loadRevenus(), this.loadRevenuPrevisions(), this.loadDepenses(), this.loadBudgets(), this.loadHistoriques(), this.loadPrevisions(), this.loadNotifications()]);
        },
        async login() {
            this.authError = '';
            this.busyAuth = true;
            this.isLoading = true;
            this.loadingProgress = 20;
            try {
                const { data } = await api.post('/auth/login', this.loginForm);
                this.auth.user = data.data;
                this.loadingProgress = 55;
                this.activeTab = 'categories';
                toastr.success(data.message);
                try {
                    await this.loadAll();
                    await this.loadNotificationPreferences();
                    this.loadingProgress = 100;
                } catch (error) {
                    toastr.error(this.errorMessage(error, 'Impossible de charger les données.'));
                }
            } catch (error) {
                this.handleAuthError(error);
            } finally {
                this.loadingProgress = 100;
                this.busyAuth = false;
                this.isLoading = false;
            }
        },
        async register() {
            this.authError = '';
            this.busyAuth = true;
            this.isLoading = true;
            this.loadingProgress = 20;
            try {
                const { data } = await api.post('/auth/register', this.registerForm);
                this.auth.user = data.data;
                this.loadingProgress = 55;
                this.activeTab = 'categories';
                toastr.success(data.message);
                try {
                    await this.loadAll();
                    await this.loadNotificationPreferences();
                    this.loadingProgress = 100;
                } catch (error) {
                    toastr.error(this.errorMessage(error, 'Impossible de charger les données.'));
                }
            } catch (error) {
                this.handleAuthError(error);
            } finally {
                this.loadingProgress = 100;
                this.busyAuth = false;
                this.isLoading = false;
            }
        },
        async logout() {
            try {
                await api.post('/auth/logout');
                this.auth.user = null;
                this.mobileSidebarOpen = false;
            } catch (error) {
                toastr.error(this.errorMessage(error, 'Impossible de fermer la session.'));
            }
        },
        async loadCategories() {
            try {
                const { data } = await api.get('/categories', { params: { search: this.categories.search } });
                const payload = data?.data ?? data;
                this.categories.items = Array.isArray(payload) ? payload : (Array.isArray(payload?.data) ? payload.data : []);
            } catch (error) {
                this.categories.items = [];
                throw error;
            }
        },
        async loadNotifications() {
            try {
                const { data } = await api.get('/notifications', { silentLoading: true });
                const items = data?.data ?? [];
                const fresh = items.filter((item) => !this.notifications.knownIds.includes(item.id_notification));
                this.notifications.items = items;
                this.notifications.knownIds = [...new Set([...this.notifications.knownIds, ...items.map((item) => item.id_notification)])];
                this.notificationUnreadCount = data?.unread_count ?? 0;
                if (this.notifications.initialized && fresh.length) {
                    fresh.reverse().forEach((item) => {
                        this.notify('info', item.titre);
                        this.playNotifEffect(item);
                    });
                }
                this.notifications.initialized = true;
            } catch (error) {
                this.notifications.items = [];
            }
        },
        async markNotificationAsRead(notification) {
            if (notification.est_lue) return;
            try {
                await api.patch(`/notifications/${notification.id_notification}/read`);
                notification.est_lue = true;
                this.notificationUnreadCount = Math.max(0, this.notificationUnreadCount - 1);
            } catch (error) { this.notify('error', this.errorMessage(error, 'Impossible de marquer la notification comme lue.')); }
        },
        async markAllNotificationsAsRead() {
            try {
                await api.patch('/notifications/read-all');
                this.notifications.items.forEach((item) => { item.est_lue = true; });
                this.notificationUnreadCount = 0;
                this.notify('success', 'Toutes les notifications ont été marquées comme lues.');
            } catch (error) { this.notify('error', this.errorMessage(error, 'Impossible de mettre à jour les notifications.')); }
        },
        async deleteNotification(notification) {
            try {
                await api.delete(`/notifications/${notification.id_notification}`);
                this.notifications.items = this.notifications.items.filter((item) => item.id_notification !== notification.id_notification);
                if (!notification.est_lue) this.notificationUnreadCount = Math.max(0, this.notificationUnreadCount - 1);
                this.notify('success', 'Notification supprimée.');
            } catch (error) { this.notify('error', this.errorMessage(error, 'Impossible de supprimer la notification.')); }
        },
        async deleteAllNotifications() {
            const confirmation = await Swal.fire({ title: 'Tout supprimer ?', text: 'Cette action est irréversible.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Supprimer', cancelButtonText: 'Annuler' });
            if (!confirmation.isConfirmed) return;
            try {
                await api.delete('/notifications');
                this.notifications.items = [];
                this.notificationUnreadCount = 0;
                this.notify('success', 'Toutes les notifications ont été supprimées.');
            } catch (error) { this.notify('error', this.errorMessage(error, 'Impossible de supprimer les notifications.')); }
        },
        async loadNotificationPreferences() {
            try {
                const { data } = await api.get('/notification-preferences');
                this.notificationPreferences = data.data;
            } catch (_) {}
        },
        async saveNotificationPreferences() {
            try {
                await api.put('/notification-preferences', this.notificationPreferences);
                this.notify('success', 'Préférences de notification enregistrées.');
            } catch (error) {
                this.notify('error', this.errorMessage(error, 'Impossible d\'enregistrer les préférences.'));
            }
        },
        playNotifEffect(notification) {
            if (this.notificationPreferences.notif_son && this.notifAudio) {
                this.notifAudio.currentTime = 0;
                this.notifAudio.play().catch(() => {});
            }
            if (this.notificationPreferences.notif_vibration && navigator.vibrate) {
                navigator.vibrate(200);
            }
            if (this.notificationPreferences.notif_navigateur && 'Notification' in window && Notification.permission === 'granted') {
                new Notification(notification.titre, { body: notification.contenu, icon: '/favicon.ico' });
            }
        },
        async requestBrowserNotificationPermission() {
            if (!('Notification' in window)) return;
            if (Notification.permission === 'granted') {
                this.browserPermission = 'granted';
                return;
            }
            const result = await Notification.requestPermission();
            this.browserPermission = result;
            if (result !== 'granted') {
                this.notificationPreferences.notif_navigateur = false;
                this.notify('info', 'Permission de notification refusée par le navigateur.');
            }
        },
        testNotificationSound() {
            if (this.notifAudio) {
                this.notifAudio.currentTime = 0;
                this.notifAudio.play().catch(() => {});
            }
            if (navigator.vibrate) navigator.vibrate(200);
        },
        async saveCategory() {
            try {
                const payload = { nom_categorie: this.categoryForm.nom_categorie };
                if (this.categoryForm.id_categorie) {
                    await api.put(`/categories/${this.categoryForm.id_categorie}`, payload);
                } else {
                    await api.post('/categories', payload);
                }
                this.categoryForm = { id_categorie: null, nom_categorie: '' };
                toastr.success('Catégorie enregistrée.');
                await this.loadCategories();
            } catch (error) {
                toastr.error(this.errorMessage(error, 'Impossible d’enregistrer la catégorie.'));
            }
        },
        editCategory(item) { this.categoryForm = { ...item }; this.activeTab = 'categories'; },
        async loadRevenus() {
            const { data } = await api.get('/revenus', { params: { search: this.revenus.search, sort: this.revenus.sort, direction: this.revenus.direction, cycle_actif: true } });
            const payload = data?.data ?? data;
            this.revenus.items = Array.isArray(payload) ? payload : (Array.isArray(payload?.data) ? payload.data : []);
        },
        async saveRevenu() {
            try {
                const payload = { ...this.revenuForm };
                if (payload.id_revenu) await api.put(`/revenus/${payload.id_revenu}`, payload);
                else await api.post('/revenus', payload);
                this.revenuForm = { id_revenu: null, source: '', montant: '', date_revenu: '', description: '' };
                toastr.success('Revenu enregistré.');
                await this.loadRevenus();
            } catch (error) {
                toastr.error(this.errorMessage(error, 'Impossible d’enregistrer le revenu.'));
            }
        },
        editRevenu(item) { this.revenuForm = { ...item }; this.activeTab = 'revenus'; },
        async loadRevenuPrevisions() {
            try {
                const { data } = await api.get('/revenu-previsions', {
                    params: {
                        search: this.revenuPrevisions.search,
                        sort: this.revenuPrevisions.sort,
                        direction: this.revenuPrevisions.direction,
                    },
                });
                const payload = data?.data ?? data;
                this.revenuPrevisions.items = Array.isArray(payload) ? payload : (Array.isArray(payload?.data) ? payload.data : []);
                this.revenuPrevisions.stats = data?.stats ?? this.revenuPrevisions.stats;
            } catch (error) {
                this.revenuPrevisions.items = [];
                this.notify('error', this.errorMessage(error, 'Impossible de charger les prévisions de revenus.'));
            }
        },
        async saveRevenuPrevision() {
            this.revenuPrevisionErrors = {};
            try {
                const payload = { ...this.revenuPrevisionForm };
                delete payload.id_revenu_prevision;
                if (this.revenuPrevisionForm.id_revenu_prevision) {
                    await api.put(`/revenu-previsions/${this.revenuPrevisionForm.id_revenu_prevision}`, payload);
                    this.notify('success', 'Prévision de revenu modifiée avec succès.');
                } else {
                    await api.post('/revenu-previsions', payload);
                    this.notify('success', 'Prévision de revenu créée avec succès.');
                }
                this.resetRevenuPrevisionForm();
                await this.loadRevenuPrevisions();
            } catch (error) {
                this.revenuPrevisionErrors = error?.response?.data?.errors ?? {};
                this.notify('error', this.errorMessage(error, 'Impossible d’enregistrer la prévision de revenu.'));
            }
        },
        async markRevenuPrevision(item) {
            const confirmation = await Swal.fire({
                title: 'Marquer ce revenu comme perçu ?',
                text: `Le revenu « ${item.source_previsionnelle} » sera enregistré dans vos revenus réels.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Oui, enregistrer le revenu',
                cancelButtonText: 'Annuler',
                reverseButtons: true,
            });

            if (!confirmation.isConfirmed) return;

            try {
                await api.post(`/revenu-previsions/${item.id_revenu_prevision}/receive`);
                this.notify('success', 'Revenu marqué comme perçu et enregistré.');
                await this.loadAll();
            } catch (error) {
                this.notify('error', this.errorMessage(error, 'Impossible d’enregistrer le revenu perçu.'));
            }
        },
        editRevenuPrevision(item) {
            this.revenuPrevisionForm = {
                id_revenu_prevision: item.id_revenu_prevision,
                montant_previsionnel: item.montant_previsionnel,
                source_previsionnelle: item.source_previsionnelle,
                date_previsionnelle: item.date_previsionnelle,
                description: item.description,
            };
            this.revenuPrevisionErrors = {};
            this.activeTab = 'previsions-revenus';
        },
        resetRevenuPrevisionForm() {
            this.revenuPrevisionForm = { id_revenu_prevision: null, montant_previsionnel: '', source_previsionnelle: '', date_previsionnelle: '', description: '' };
            this.revenuPrevisionErrors = {};
        },
        revenuPrevisionStatusClass(status) {
            if (status === 'Réalisée') return 'bg-emerald-50 text-emerald-700';
            if (status === "Aujourd'hui") return 'bg-indigo-50 text-indigo-700';
            if (status === 'À venir') return 'bg-amber-50 text-amber-700';
            return 'bg-rose-50 text-rose-700';
        },
        async loadDepenses() {
            const { data } = await api.get('/depenses', { params: { search: this.depenses.search, sort: this.depenses.sort, direction: this.depenses.direction, cycle_actif: true } });
            const payload = data?.data ?? data;
            this.depenses.items = Array.isArray(payload) ? payload : (Array.isArray(payload?.data) ? payload.data : []);
        },
        async saveDepense() {
            try {
                const payload = { ...this.depenseForm };
                if (payload.id_depense) await api.put(`/depenses/${payload.id_depense}`, payload);
                else await api.post('/depenses', payload);
                this.depenseForm = { id_depense: null, id_categorie: '', montant: '', date_depense: '', description: '' };
                toastr.success('Dépense enregistrée.');
                await this.loadDepenses();
                await this.loadBudgets();
            } catch (error) {
                toastr.error(this.errorMessage(error, 'Impossible d’enregistrer la dépense.'));
            }
        },
        editDepense(item) { this.depenseForm = { ...item }; this.activeTab = 'depenses'; },
        async loadBudgets() {
            try {
                const { data } = await api.get('/budgets', {
                    params: {
                        search: this.budgets.search,
                        sort: this.budgets.sort,
                        direction: this.budgets.direction,
                    },
                });
                const payload = data?.data ?? data;
                const activeBudgets = Array.isArray(payload) ? payload : (Array.isArray(payload?.data) ? payload.data : []);
                this.budgets.items = [...activeBudgets, ...(data?.historiques ?? [])];
                this.budgets.stats = data?.stats ?? { total: 0, actifs: 0, montant_total: 0 };
                const budgetActif = activeBudgets[0];
                if (budgetActif && !this.budgetForm.id_budget) this.editBudget(budgetActif);
            } catch (error) {
                this.budgets.items = [];
                this.notify('error', this.errorMessage(error, 'Impossible de charger les budgets.'));
            }
        },
        async loadHistoriques() {
            const { data } = await api.get('/budgets/historiques');
            this.historiques.items = data?.data ?? [];
        },
        async loadPrevisions() {
            try {
                const { data } = await api.get('/depense-previsions', {
                    params: {
                        search: this.previsions.search,
                        sort: this.previsions.sort,
                        direction: this.previsions.direction,
                    },
                });
                const payload = data?.data ?? data;
                this.previsions.items = Array.isArray(payload) ? payload : (Array.isArray(payload?.data) ? payload.data : []);
                this.previsions.stats = data?.stats ?? this.previsions.stats;
            } catch (error) {
                this.previsions.items = [];
                this.notify('error', this.errorMessage(error, 'Impossible de charger les prévisions.'));
            }
        },
        async savePrevision() {
            this.previsionErrors = {};
            try {
                const payload = { ...this.previsionForm };
                delete payload.id_depense_prevision;
                if (this.previsionForm.id_depense_prevision) {
                    await api.put(`/depense-previsions/${this.previsionForm.id_depense_prevision}`, payload);
                    this.notify('success', 'Prévision modifiée avec succès.');
                } else {
                    await api.post('/depense-previsions', payload);
                    this.notify('success', 'Prévision créée avec succès.');
                }
                this.resetPrevisionForm();
                await this.loadPrevisions();
            } catch (error) {
                this.previsionErrors = error?.response?.data?.errors ?? {};
                this.notify('error', this.errorMessage(error, 'Impossible d’enregistrer la prévision.'));
            }
        },
        async validatePrevision(item) {
            const confirmation = await Swal.fire({
                title: 'Valider cette prévision ?',
                text: `Une dépense sera enregistrée pour « ${item.description} » et la prévision sera clôturée.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Oui, enregistrer la dépense',
                cancelButtonText: 'Annuler',
                reverseButtons: true,
            });

            if (!confirmation.isConfirmed) return;

            try {
                await api.post(`/depense-previsions/${item.id_depense_prevision}/validate`);
                this.notify('success', 'Prévision validée et dépense enregistrée.');
                await this.loadAll();
            } catch (error) {
                this.notify('error', this.errorMessage(error, 'Impossible de valider la prévision.'));
            }
        },
        editPrevision(item) {
            this.previsionForm = {
                id_depense_prevision: item.id_depense_prevision,
                id_categorie: item.id_categorie,
                montant_previsionnel: item.montant_previsionnel,
                date_previsionnelle: item.date_previsionnelle,
                description: item.description,
            };
            this.previsionErrors = {};
            this.activeTab = 'previsions';
        },
        resetPrevisionForm() {
            this.previsionForm = { id_depense_prevision: null, id_categorie: '', montant_previsionnel: '', date_previsionnelle: '', description: '' };
            this.previsionErrors = {};
        },
        previsionStatusClass(status) {
            if (status === "Aujourd'hui") return 'bg-indigo-50 text-indigo-700';
            if (status === 'À venir') return 'bg-amber-50 text-amber-700';
            return 'bg-rose-50 text-rose-700';
        },
        async saveBudget() {
            this.budgetErrors = {};
            try {
                const payload = { ...this.budgetForm };
                delete payload.id_budget;
                if (this.budgetForm.id_budget) {
                    await api.put(`/budgets/${this.budgetForm.id_budget}`, payload);
                    this.notify('success', 'Budget modifié avec succès.');
                } else {
                    await api.post('/budgets', payload);
                    this.notify('success', 'Budget créé avec succès.');
                }
                this.resetBudgetForm();
                await this.loadBudgets();
            } catch (error) {
                this.budgetErrors = error?.response?.data?.errors ?? {};
                this.notify('error', this.errorMessage(error, 'Impossible d’enregistrer le budget.'));
            }
        },
        editBudget(item) {
            this.budgetForm = { ...item, reinitialiser_solde: false };
            this.budgetErrors = {};
            this.activeTab = 'budgets';
        },
        resetBudgetForm() {
            this.budgetForm = this.budgets.items.length === 1
                ? { ...this.budgets.items[0], reinitialiser_solde: false }
                : { id_budget: null, periode: '', montant_total: '', date_debut: '', date_fin: '', reinitialiser_solde: false };
            this.budgetErrors = {};
        },
        formatMoney(value) {
            return Number(value || 0).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        formatDate(value) {
            if (!value) return '-';

            const [year, month, day] = value.substring(0, 10).split('-');
            return `${day}/${month}/${year}`;
        },
        formatDateTime(value) {
            if (!value) return '-';
            return new Intl.DateTimeFormat('fr-FR', { dateStyle: 'short', timeStyle: 'short' }).format(new Date(value));
        },
        statusClass(status) {
            if (status === 'En cours') return 'bg-emerald-50 text-emerald-700';
            if (status === 'À venir') return 'bg-amber-50 text-amber-700';
            return 'bg-slate-100 text-slate-600';
        },
        notify(type, message) {
            if (window.toastr && typeof window.toastr[type] === 'function') {
                window.toastr[type](message);
            }
        },
        async destroy(resource, id) {
            const ok = await Swal.fire({ title: 'Confirmer', text: 'Action irréversible.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Supprimer' });
            if (!ok.isConfirmed) return;
            try {
                await api.delete(`/${resource}/${id}`);
                await this.loadAll();
                toastr.success('Élément supprimé.');
            } catch (error) {
                toastr.error(this.errorMessage(error, 'Impossible de supprimer cet élément.'));
            }
        },
        errorMessage(error, fallback) {
            const response = error?.response;
            const data = response?.data;
            if (data?.errors) {
                const firstKey = Object.keys(data.errors)[0];
                if (firstKey && data.errors[firstKey]?.[0]) return data.errors[firstKey][0];
            }
            if (response?.status === 401) return 'Session expirée. Veuillez vous reconnecter.';
            if (response?.status === 419) return 'Session expirée. Actualisez la page puis réessayez.';
            return data?.message || fallback;
        },
        handleAuthError(error) {
            const status = error?.response?.status;
            const data = error?.response?.data;
            this.authError = data?.message || (status === 422 ? 'Veuillez vérifier les champs saisis.' : 'Une erreur est survenue.');
            if (data?.errors) {
                const firstKey = Object.keys(data.errors)[0];
                if (firstKey) {
                    this.authError = data.errors[firstKey][0];
                }
            }
            if (window.toastr) {
                toastr.error(this.authError);
            }
        },
    }
}).mount('#app');
</script>
</body>
</html>
