import React from "react";
import { createRoot } from "react-dom/client";
import "../styles/index.scss";
// import App from "@/react/components/App";
import { AuthProvider } from "@/react/app/providers/AuthProvider";
// import { DeviceProvider } from "@/react/app/providers/DeviceProvider";
import App from "@/react/App";
import {DeviceProvider} from "@/react/hooks/DeviceProvider"; // ou le chemin correct

const container = document.getElementById("root");

if (container) {
    createRoot(container).render(
        <React.StrictMode>
            <AuthProvider>
                <DeviceProvider>
                    <App />
                </DeviceProvider>
            </AuthProvider>
        </React.StrictMode>
    );
}
