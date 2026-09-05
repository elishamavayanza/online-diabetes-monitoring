/**
 * Converts the value stored by the API into an URL usable by an <img> tag.
 * Uploaded avatars are stored under /uploads/files/avatars.  Older records
 * contain only the filename, whereas newer API responses contain that path.
 */
export function resolveAvatarUrl(value?: string | null): string | undefined {
    if (!value) return undefined;

    if (/^(https?:|data:|blob:)/i.test(value)) return value;

    const path = value.startsWith('/')
        ? value
        : `/uploads/files/avatars/${value}`;
    const baseUrl = (import.meta as unknown as { env: { VITE_API_BASE_URL?: string } }).env.VITE_API_BASE_URL || '';

    return `${baseUrl.replace(/\/$/, '')}${path}`;
}
