export interface ApiFeedback<T> {
    status: number;
    flush?: string;
    flushDescription?: string;
    errors?: Record<string, string>;
    warnings?: Record<string, string>;
    data: T;
    error?: boolean;
    message?: string;
}

export function hasApiErrors<T>(feedback: ApiFeedback<T>): boolean {
    if (feedback.error) return true;
    if (feedback.errors && Object.keys(feedback.errors).length > 0) return true;
    return false;
}

export function getApiErrorMessage<T>(feedback: ApiFeedback<T>, fallback = 'Une erreur est survenue.'): string {
    if (feedback.message) return feedback.message;
    if (feedback.flushDescription) return feedback.flushDescription;
    if (feedback.errors) {
        const first = Object.values(feedback.errors)[0];
        if (first) return first;
    }
    return fallback;
}

export function unwrapApiData<T>(feedback: ApiFeedback<T>, fallback = 'Une erreur est survenue.'): T {
    if (hasApiErrors(feedback)) {
        throw new Error(getApiErrorMessage(feedback, fallback));
    }
    return feedback.data;
}
