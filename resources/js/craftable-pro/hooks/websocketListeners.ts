export const setupWebSocketListeners = (
    connect: () => void,
    disconnect: () => void,
) => {
    window.Echo.connector.socket.on('connect', () => {
        console.log('Connection established');
        connect();
    });

    window.Echo.connector.socket.on('connect_error', (error: Error) => {
        console.error('Connection error:', error);
        disconnect();
    });

    window.Echo.connector.socket.on('reconnect_error', (error: Error) => {
        console.error('Reconnection error:', error);
        disconnect();
    });

    window.Echo.connector.socket.on('disconnect', () => {
        console.warn('Connection lost.');
        disconnect();
    });
};

export const removeWebSocketListeners = () => {
    const socket = window.Echo.connector.socket;

    socket.off('connect');
    socket.off('connect_error');
    socket.off('reconnect_error');
    socket.off('disconnect');

    console.log('All WebSocket listeners removed');
};
