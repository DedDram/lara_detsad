window.Echo.channel('admin-channel')
    .listen('ReviewAdded', (event) => {
        console.log('Revie - ReviewAdded:', event);
        alert('Revie: ' + JSON.stringify(event));
    });
