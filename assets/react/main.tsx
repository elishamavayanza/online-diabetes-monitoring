import React from "react";
import { createRoot } from "react-dom/client";
import "../styles/index.scss";
import { AuthProvider } from "@/react/app/providers/AuthProvider";
import AppRoutes from "@/react/app/routes/AppRoutes";
import {DeviceProvider} from "@/react/hooks/DeviceProvider";


const container = document.getElementById("root");

if (container) {
    createRoot(container).render(
        <React.StrictMode>
            <AuthProvider>
                <DeviceProvider>
                    <AppRoutes />
                </DeviceProvider>
            </AuthProvider>
        </React.StrictMode>
    );
}
