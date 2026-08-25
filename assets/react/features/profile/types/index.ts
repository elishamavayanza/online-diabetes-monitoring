export interface UserProfileData {
    id: string;
    name: string;
    email: string;
    role: string;
    phone?: string;
    avatarUrl?: string;
    locale?: string;
}

export interface ProfileUpdatePayload {
    name: string;
    phone?: string;
    avatarUrl?: string;
}
