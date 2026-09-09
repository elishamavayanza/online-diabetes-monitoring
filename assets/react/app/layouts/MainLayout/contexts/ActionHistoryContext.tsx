// contexts/ActionHistoryContext.tsx
import React, { createContext, useCallback, useContext, useMemo, useRef } from 'react';

const DEFAULT_MAX_DEPTH = 100;

interface ActionHistoryEntry {
    key?: string;
    run: () => void;
}

export interface PushActionOptions {
    /** Si la dernière action empilée porte la même clé, elle est remplacée au lieu d'en empiler une nouvelle. */
    key?: string;
}

interface ActionHistoryContextValue {
    pushAction: (action: () => void, options?: PushActionOptions) => void;
    undoLastAction: () => boolean; // retourne true si une action a été annulée
    clearHistory: () => void;
    canUndo: () => boolean;
}

const ActionHistoryContext = createContext<ActionHistoryContextValue | undefined>(undefined);

export function ActionHistoryProvider({
    children,
    maxDepth = DEFAULT_MAX_DEPTH,
}: {
    children: React.ReactNode;
    maxDepth?: number;
}) {
    const actionsRef = useRef<ActionHistoryEntry[]>([]);

    const pushAction = useCallback(
        (action: () => void, options?: PushActionOptions) => {
            const stack = actionsRef.current;
            const key = options?.key;

            if (key !== undefined && stack.length > 0 && stack[stack.length - 1].key === key) {
                stack[stack.length - 1] = { key, run: action };
                return;
            }

            stack.push({ key, run: action });

            if (stack.length > maxDepth) {
                // Évince les actions les plus anciennes pour éviter une croissance
                // illimitée en mémoire (une session longue ne peut plus saturer).
                stack.splice(0, stack.length - maxDepth);
            }
        },
        [maxDepth]
    );

    const undoLastAction = useCallback((): boolean => {
        const stack = actionsRef.current;
        while (stack.length > 0) {
            const entry = stack.pop();
            if (!entry) {
                break;
            }
            try {
                entry.run();
                return true;
            } catch (error) {
                // Une action défaillante ne doit jamais faire planter l'application :
                // on la retire et on tente l'action suivante.
                console.error(
                    "[ActionHistory] Une action d'annulation a échoué et a été ignorée.",
                    error
                );
            }
        }
        return false;
    }, []);

    const clearHistory = useCallback(() => {
        actionsRef.current = [];
    }, []);

    const canUndo = useCallback((): boolean => actionsRef.current.length > 0, []);

    // Les fonctions sont stables : empiler/dépiler ne déclenche aucun re-render
    // ni des consommateurs, ni du provider (aucun state utilisé).
    const value = useMemo(
        () => ({ pushAction, undoLastAction, clearHistory, canUndo }),
        [pushAction, undoLastAction, clearHistory, canUndo]
    );

    return <ActionHistoryContext.Provider value={value}>{children}</ActionHistoryContext.Provider>;
}

export function useActionHistory() {
    const context = useContext(ActionHistoryContext);
    if (!context) {
        throw new Error('useActionHistory must be used within an ActionHistoryProvider');
    }
    return context;
}