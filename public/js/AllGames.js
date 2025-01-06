function deleteGame(uuid) {
    if (!confirm('Opravdu chcete tuto hru smazat?')) return;

    fetch(`/games/${uuid}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    });
}

