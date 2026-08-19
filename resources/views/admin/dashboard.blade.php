<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Tableau de bord administrateur
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @forelse ($rendezVous as $rendezVousItem)
                        <div class="mb-4 p-4 border border-gray-200 dark:border-gray-700 rounded">
                            <p>
                                Client : {{ $rendezVousItem->user->name }}
                            </p>
                            <p>
                                Créneau : {{ $rendezVousItem->creneau->date->format('d/m/Y') }}
                                à {{ $rendezVousItem->creneau->heure_debut->format('H:i') }}
                            </p>
                            <p>
                                Statut :
                                @if ($rendezVousItem->statut === 'annule')
                                    <span class="text-red-600 dark:text-red-400">
                                        Annulé
                                    </span>
                                @elseif ($rendezVousItem->statut === 'confirme')
                                    Confirmé
                                @else
                                    En attente
                                @endif
                            </p>
                        </div>
                    @empty
                        <p>Aucun rendez-vous.</p>
                    @endforelse

                </div>
            </div>
        </div>
    </div>
</x-app-layout>