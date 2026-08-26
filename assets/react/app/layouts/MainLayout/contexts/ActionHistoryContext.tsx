// contexts/ActionHistoryContext.tsx
import React, { createContext, useContext, useRef, useCallback, useState } from 'react';

interface ActionHistoryContextValue {
    pushAction: (action: () => void) => void;
    undoLastAction: () => boolean; // retourne true si une action a été annulée
}

const ActionHistoryContext = createContext<ActionHistoryContextValue | undefined>(undefined);

export function ActionHistoryProvider({ children }: { children: React.ReactNode }) {
    const actionsRef = useRef<(() => void)[]>([]);

    const pushAction = useCallback((action: () => void) => {
        actionsRef.current.push(action);
    }, []);

    const undoLastAction = useCallback((): boolean => {
        const action = actionsRef.current.pop();
        if (action) {
            action();
            return true;
        }
        return false;
    }, []);

    return (
        <ActionHistoryContext.Provider value={{ pushAction, undoLastAction }}>
            {children}
        </ActionHistoryContext.Provider>
    );
}

export function useActionHistory() {
    const context = useContext(ActionHistoryContext);
    if (!context) {
        throw new Error('useActionHistory must be used within an ActionHistoryProvider');
    }
    return context;
}
