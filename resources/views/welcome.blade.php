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
    <div v-if="mobileSidebarOpen" @click="mobileSidebarOpen=false" class="fixed inset-0 z-30 bg-slate-950/50 lg:hidden"></div>
    <div class="min-h-screen lg:flex">
        <aside class="fixed inset-y-0 left-0 z-40 w-72 bg-slate-950 text-slate-100 p-6 transform transition-transform duration-200 lg:static lg:translate-x-0 lg:min-h-screen"
            :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
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
                    <div class="flex items-center gap-3">
                        <button @click="mobileSidebarOpen = !mobileSidebarOpen" class="lg:hidden h-11 w-11 inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-700">
                            <i class="fa-solid fa-bars"></i>
                        </button>
                        <div>
                        <p class="text-sm text-slate-500">Gestion des finances personnelles</p>
                        <h2 class="text-xl font-semibold">@{{ title }}</h2>
                        </div>
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
                    <div class="grid md:grid-cols-4 gap-4">
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

                    <div v-if="activeTab==='budgets'" class="space-y-6">
                        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                            <div>
                                <p class="text-sm font-medium text-indigo-600">Planification financière</p>
                                <h3 class="mt-1 text-2xl font-semibold tracking-tight text-slate-950">Mes budgets</h3>
                                <p class="mt-2 text-sm text-slate-500">Créez une enveloppe, suivez sa période et gardez le contrôle de vos objectifs.</p>
                            </div>
                            <a href="{{ url('/budgets') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-indigo-200 bg-white px-4 py-3 text-sm font-semibold text-indigo-600 transition hover:bg-indigo-50">
                                <i class="fa-solid fa-expand"></i>
                                Vue détaillée
                            </a>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-3">
                            <div class="rounded-3xl border border-indigo-100 bg-indigo-50 p-5"><div class="text-sm font-medium text-indigo-600">Total budgets</div><div class="mt-2 text-3xl font-bold text-slate-950">@{{ budgets.stats.total }}</div></div>
                            <div class="rounded-3xl border border-emerald-100 bg-emerald-50 p-5"><div class="text-sm font-medium text-emerald-600">Budget actif</div><div class="mt-2 text-3xl font-bold text-slate-950">@{{ budgets.stats.actifs }}</div></div>
                            <div class="rounded-3xl border border-violet-100 bg-violet-50 p-5"><div class="text-sm font-medium text-violet-600">Montant budgété</div><div class="mt-2 text-3xl font-bold text-slate-950">@{{ formatMoney(budgets.stats.montant_total) }} <span class="text-sm font-semibold text-slate-500">FC</span></div></div>
                        </div>

                        <div class="grid gap-6 lg:grid-cols-3">
                            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
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
                                    <div class="grid grid-cols-2 gap-3">
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
                                    <div class="flex gap-3 pt-2">
                                        <button type="submit" class="flex-1 rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700">@{{ budgetForm.id_budget ? 'Mettre à jour' : 'Créer le budget' }}</button>
                                        <button v-if="budgetForm.id_budget" type="button" @click="resetBudgetForm" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-500 transition hover:bg-slate-50">Annuler</button>
                                    </div>
                                </form>
                            </div>

                            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
                                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                                    <div>
                                        <h4 class="text-lg font-semibold text-slate-950">Budgets enregistrés</h4>
                                        <p class="mt-1 text-sm text-slate-500">@{{ budgets.stats.total }} budget(s) au total</p>
                                    </div>
                                    <div class="flex gap-3">
                                        <input v-model="budgets.search" @input="debouncedLoadBudgets" placeholder="Rechercher" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 sm:w-44">
                                        <select v-model="budgets.sort" @change="loadBudgets" class="rounded-2xl border border-slate-300 bg-white px-3 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                                            <option value="date_debut">Date</option><option value="periode">Période</option><option value="montant_total">Montant</option>
                                        </select>
                                    </div>
                                </div>

                                <div v-if="!budgets.items.length" class="rounded-2xl bg-slate-50 px-5 py-12 text-center text-sm text-slate-500">
                                    <i class="fa-solid fa-chart-pie mb-3 text-2xl text-indigo-400"></i>
                                    <p>Aucun budget ne correspond à votre recherche.</p>
                                </div>

                                <div v-else class="mt-5 overflow-x-auto">
                                    <table class="w-full min-w-[680px] text-sm">
                                        <thead class="border-b border-slate-100 text-left text-xs uppercase tracking-wider text-slate-400"><tr><th class="py-3">Période</th><th class="py-3">Montant</th><th class="py-3">Dates</th><th class="py-3">Statut</th><th></th></tr></thead>
                                        <tbody>
                                            <tr v-for="item in budgets.items" :key="item.id_budget" class="border-b border-slate-100 last:border-0">
                                                <td class="py-4 font-semibold text-slate-900">@{{ item.periode }}</td>
                                                <td class="py-4 font-semibold text-slate-700">@{{ formatMoney(item.montant_total) }} <span class="text-xs text-slate-400">FC</span></td>
                                                <td class="py-4 text-slate-500">@{{ formatDate(item.date_debut) }} → @{{ formatDate(item.date_fin) }}</td>
                                                <td class="py-4"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold" :class="statusClass(item.statut)">@{{ item.statut }}</span></td>
                                                <td class="py-4 text-right"><button @click="editBudget(item)" class="mr-3 text-indigo-600">Modifier</button><button @click="destroy('budgets', item.id_budget)" class="text-rose-600">Supprimer</button></td>
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
const debounce = (fn, wait = 300) => { let t; return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), wait); }; };

Vue.createApp({
    data() {
        return {
            activeTab: 'categories',
            authMode: 'login',
            auth: { user: null },
            mobileSidebarOpen: false,
            busyAuth: false,
            authError: '',
            tabs: [
                { key: 'categories', label: 'Catégories', icon: 'fa-solid fa-tags' },
                { key: 'revenus', label: 'Revenus', icon: 'fa-solid fa-coins' },
                { key: 'depenses', label: 'Dépenses', icon: 'fa-solid fa-receipt' },
                { key: 'budgets', label: 'Budgets', icon: 'fa-solid fa-chart-pie' },
            ],
            loginForm: { email: '', mot_de_passe: '' },
            registerForm: { nom: '', prenom: '', email: '', mot_de_passe: '', mot_de_passe_confirmation: '' },
            categoryForm: { id_categorie: null, nom_categorie: '' },
            revenuForm: { id_revenu: null, source: '', montant: '', date_revenu: '', description: '' },
            depenseForm: { id_depense: null, id_categorie: '', montant: '', date_depense: '', description: '' },
            budgetForm: { id_budget: null, periode: '', montant_total: '', date_debut: '', date_fin: '' },
            budgetErrors: {},
            categories: { items: [], search: '' },
            revenus: { items: [], search: '', sort: 'date_revenu', direction: 'desc' },
            depenses: { items: [], search: '', sort: 'date_depense', direction: 'desc' },
            budgets: { items: [], search: '', sort: 'date_debut', direction: 'desc', stats: { total: 0, actifs: 0, montant_total: 0 } },
            debouncedLoadCategories: null,
            debouncedLoadRevenus: null,
            debouncedLoadDepenses: null,
            debouncedLoadBudgets: null,
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
        this.debouncedLoadBudgets = debounce(() => this.loadBudgets(), 250);
        this.bootstrap();
    },
    methods: {
        async bootstrap() {
            try {
                const { data } = await api.get('/auth/me');
                this.auth.user = data.data;
                await this.loadAll();
            } catch (error) {
                if (error?.response?.status !== 401) {
                    toastr.error(this.errorMessage(error, 'Impossible de contacter le serveur.'));
                }
            }
        },
        async loadAll() {
            await this.loadCategories();
            await Promise.allSettled([this.loadRevenus(), this.loadDepenses(), this.loadBudgets()]);
        },
        async login() {
            this.authError = '';
            this.busyAuth = true;
            try {
                const { data } = await api.post('/auth/login', this.loginForm);
                this.auth.user = data.data;
                this.activeTab = 'categories';
                toastr.success(data.message);
                try {
                    await this.loadAll();
                } catch (error) {
                    toastr.error(this.errorMessage(error, 'Impossible de charger les données.'));
                }
            } catch (error) {
                this.handleAuthError(error);
            } finally {
                this.busyAuth = false;
            }
        },
        async register() {
            this.authError = '';
            this.busyAuth = true;
            try {
                const { data } = await api.post('/auth/register', this.registerForm);
                this.auth.user = data.data;
                this.activeTab = 'categories';
                toastr.success(data.message);
                try {
                    await this.loadAll();
                } catch (error) {
                    toastr.error(this.errorMessage(error, 'Impossible de charger les données.'));
                }
            } catch (error) {
                this.handleAuthError(error);
            } finally {
                this.busyAuth = false;
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
            const { data } = await api.get('/revenus', { params: { search: this.revenus.search, sort: this.revenus.sort, direction: this.revenus.direction } });
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
        async loadDepenses() {
            const { data } = await api.get('/depenses', { params: { search: this.depenses.search, sort: this.depenses.sort, direction: this.depenses.direction } });
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
                this.budgets.items = Array.isArray(payload) ? payload : (Array.isArray(payload?.data) ? payload.data : []);
                this.budgets.stats = data?.stats ?? { total: 0, actifs: 0, montant_total: 0 };
            } catch (error) {
                this.budgets.items = [];
                this.notify('error', this.errorMessage(error, 'Impossible de charger les budgets.'));
            }
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
            this.budgetForm = { ...item };
            this.budgetErrors = {};
            this.activeTab = 'budgets';
        },
        resetBudgetForm() {
            this.budgetForm = { id_budget: null, periode: '', montant_total: '', date_debut: '', date_fin: '' };
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
