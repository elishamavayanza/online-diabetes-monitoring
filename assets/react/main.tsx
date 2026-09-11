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
                <AuthProvider>
                    <I18nProvider>
                        <ToastProvider>
                            <DeviceProvider>
                                <ActionHistoryProvider>
                                    <AppRoutes />
                                </ActionHistoryProvider>
                            </DeviceProvider>
                        </ToastProvider>
                    </I18nProvider>
                </AuthProvider>
            </ThemeProvider>
        </React.StrictMode>
    );
}
