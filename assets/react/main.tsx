import React from "react";
import { createRoot } from "react-dom/client";
import "../styles/index.scss";
import { AuthProvider } from "@/react/app/providers/AuthProvider";
import AppRoutes from "@/react/app/routes/AppRoutes";
import {DeviceProvider} from "@/react/hooks/DeviceProvider";
import {ThemeProvider} from "@/react/hooks/ThemeProvider";
import { ActionHistoryProvider} from "@/react/app/layouts/MainLayout/contexts/ActionHistoryContext";
import { ToastProvider } from "@/react/app/layouts/MainLayout/contexts/ToastContext";
import { I18nProvider } from "@/react/i18n/I18nContext";


const container = document.getElementById("root");

if (container) {
    createRoot(container).render(
        <React.StrictMode>
            <ThemeProvider>
                <ToastProvider>
                    <AuthProvider>
                        <I18nProvider>
                            <DeviceProvider>
                                <ActionHistoryProvider>
                                    <AppRoutes />
                                </ActionHistoryProvider>
                            </DeviceProvider>
                        </I18nProvider>
                    </AuthProvider>
                </ToastProvider>
            </ThemeProvider>
        </React.StrictMode>
    );
}
