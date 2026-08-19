import React from "react";
import { createRoot } from "react-dom/client";
import "../styles/index.scss";
import HomePage from './pages/HomePage/HomePage';

console.log("🚀 main.tsx exécuté !");

const container = document.getElementById("root");

if (container) {
    createRoot(container).render(
        <React.StrictMode>
            <HomePage />
        </React.StrictMode>
    );
}
