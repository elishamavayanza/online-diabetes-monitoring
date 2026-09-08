import React from "react";
import { createRoot } from "react-dom/client";
import "../styles/index.scss";
import { AuthProvider } from "@/react/app/providers/AuthProvider";
import AppRoutes from "@/react/app/routes/AppRoutes";
import {DeviceProvider} from "@/react/hooks/DeviceProvider";
import {ThemeProvider} from "@/react/hooks/ThemeProvider";
import {ActionHistoryProvider} from "@/react/app/layouts/MainLayout/contexts/ActionHistoryContext";
import { ToastProvider } from "@/react/app/layouts/MainLayout/contexts/ToastContext";


const container = document.getElementById("root");

if (container) {
    createRoot(container).render(
        <React.StrictMode>
            <ThemeProvider>
                <ToastProvider>
                    <AuthProvider>
                        <DeviceProvider>
                            <ActionHistoryProvider>
                                <AppRoutes />
                            </ActionHistoryProvider>
                        </DeviceProvider>
                    </AuthProvider>
                </ToastProvider>
            </ThemeProvider>
        </React.StrictMode>
    );
}
