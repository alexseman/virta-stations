import React from 'react';
import ReactDOM from 'react-dom/client';
import App from './App.tsx';
import './index.css';

const rootElement: HTMLElement = document.getElementById('root') as HTMLElement;
rootElement.classList.add('min-h-screen');
ReactDOM.createRoot(rootElement).render(
    <React.StrictMode>
        <App />
    </React.StrictMode>
);
