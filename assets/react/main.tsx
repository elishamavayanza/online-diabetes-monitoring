import React from "react";
import { createRoot } from "react-dom/client";
import "../styles/index.scss";
import App from "@/react/App";
import { AuthProvider } from "@/react/app/providers/AuthProvider";
import AppRoutes from "@/react/app/routes/AppRoutes";

const container = document.getElementById("root");

if (container) {
    createRoot(container).render(
        <React.StrictMode>
            <AuthProvider>
                <AppRoutes />
            </AuthProvider>
        </React.StrictMode>
    );
}
