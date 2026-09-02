import { useEffect, useRef, useState } from 'react';
import { tokenStorage } from '@/services/storage/storage.service';
import { MessageAttachment } from '../types';
import {
    DownloadIcon,
    ZoomInIcon,
    FileIcon,
    PlayIcon,
    PauseIcon,
    MicIcon,
} from './MessageIcons';

interface AttachmentPreviewProps {
    attachment: MessageAttachment;
}

export function AttachmentPreview({ attachment }: AttachmentPreviewProps) {
    const [objectUrl, setObjectUrl] = useState<string | null>(null);
    const [loadError, setLoadError] = useState(false);
    const [showFullImage, setShowFullImage] = useState(false);
    const [showFullVideo, setShowFullVideo] = useState(false);

    // ============================================================
    // AUDIO
    // ============================================================

    const [isAudioPlaying, setIsAudioPlaying] = useState(false);
    const [currentTime, setCurrentTime] = useState(0);
    const [duration, setDuration] = useState(0);

    const audioRef = useRef<HTMLAudioElement>(null);
    const progressBarRef = useRef<HTMLDivElement>(null);

    // ============================================================
    // VIDEO
    // ============================================================

    const [isVideoPlaying, setIsVideoPlaying] = useState(false);
    const [videoCurrentTime, setVideoCurrentTime] = useState(0);
    const [videoDuration, setVideoDuration] = useState(0);

    const videoRef = useRef<HTMLVideoElement>(null);
    const modalVideoRef = useRef<HTMLVideoElement>(null);
    const videoProgressBarRef = useRef<HTMLDivElement>(null);

    // ============================================================
    // IDENTIFICATION DU TYPE DE FICHIER
    // ============================================================

    const fileName = attachment.fileName.toLowerCase();
    const mimeType = attachment.mimeType.toLowerCase();

    /*
     * IMPORTANT :
     *
     * Un message vocal enregistré avec MediaRecorder peut être :
     *
     *   audio/webm
     *   video/webm
     *
     * selon le navigateur/backend.
     *
     * On reconnaît donc également les fichiers dont le nom commence
     * par "vocal-" comme des messages audio.
     */
    const isVoiceMessage =
        mimeType.startsWith('audio/') ||
        fileName.startsWith('vocal-') ||
        fileName.includes('vocal') ||
        fileName.includes('message-vocal');

    const isImage = mimeType.startsWith('image/');

    const isVideo =
        mimeType.startsWith('video/') &&
        !isVoiceMessage;

    // ============================================================
    // RÉCUPÉRATION DU FICHIER AVEC AUTHENTIFICATION
    // ============================================================

    useEffect(() => {
        const controller = new AbortController();
        const token = tokenStorage.getAccessToken();

        setObjectUrl(null);
        setLoadError(false);

        fetch(attachment.fileUrl, {
            headers: token
                ? {
                    Authorization: `Bearer ${token}`,
                }
                : {},
            signal: controller.signal,
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error('Téléchargement impossible');
                }

                return response.blob();
            })
            .then((blob) => {
                setObjectUrl(URL.createObjectURL(blob));
            })
            .catch((error: unknown) => {
                if (
                    !(
                        error instanceof DOMException &&
                        error.name === 'AbortError'
                    )
                ) {
                    setLoadError(true);
                }
            });

        return () => {
            controller.abort();

            setObjectUrl((currentUrl) => {
                if (currentUrl) {
                    URL.revokeObjectURL(currentUrl);
                }

                return null;
            });
        };
    }, [attachment.fileUrl]);

    // ============================================================
    // GESTION AUDIO
    // ============================================================

    useEffect(() => {
        const audio = audioRef.current;

        if (!audio) {
            return;
        }

        const handleTimeUpdate = () => {
            setCurrentTime(audio.currentTime);
        };

        const handleLoadedMetadata = () => {
            if (Number.isFinite(audio.duration)) {
                setDuration(audio.duration);
            }
        };

        const handleEnded = () => {
            setIsAudioPlaying(false);
            setCurrentTime(0);
        };

        const handlePlay = () => {
            setIsAudioPlaying(true);
        };

        const handlePause = () => {
            setIsAudioPlaying(false);
        };

        audio.addEventListener('timeupdate', handleTimeUpdate);
        audio.addEventListener('loadedmetadata', handleLoadedMetadata);
        audio.addEventListener('ended', handleEnded);
        audio.addEventListener('play', handlePlay);
        audio.addEventListener('pause', handlePause);

        return () => {
            audio.removeEventListener('timeupdate', handleTimeUpdate);
            audio.removeEventListener(
                'loadedmetadata',
                handleLoadedMetadata
            );
            audio.removeEventListener('ended', handleEnded);
            audio.removeEventListener('play', handlePlay);
            audio.removeEventListener('pause', handlePause);
        };
    }, [objectUrl]);

    // ============================================================
    // LECTURE / PAUSE AUDIO
    // ============================================================

    const toggleAudio = () => {
        const audio = audioRef.current;

        if (!audio) {
            return;
        }

        if (audio.paused) {
            void audio.play();
        } else {
            audio.pause();
        }
    };

    // ============================================================
    // RECHERCHE DANS L'AUDIO
    // ============================================================

    const handleSeek = (e: React.MouseEvent<HTMLDivElement>) => {
        const audio = audioRef.current;
        const bar = progressBarRef.current;

        if (!audio || !bar || !duration) {
            return;
        }

        const rect = bar.getBoundingClientRect();

        const x = e.clientX - rect.left;

        const percentage = Math.max(
            0,
            Math.min(1, x / rect.width)
        );

        audio.currentTime = percentage * duration;

        setCurrentTime(audio.currentTime);
    };

    // ============================================================
    // GESTION VIDÉO
    // ============================================================

    useEffect(() => {
        const video = videoRef.current;

        if (!video) {
            return;
        }

        const handleTimeUpdate = () => {
            setVideoCurrentTime(video.currentTime);
        };

        const handleLoadedMetadata = () => {
            if (Number.isFinite(video.duration)) {
                setVideoDuration(video.duration);
            }
        };

        const handleEnded = () => {
            setIsVideoPlaying(false);
        };

        const handlePlay = () => {
            setIsVideoPlaying(true);
        };

        const handlePause = () => {
            setIsVideoPlaying(false);
        };

        video.addEventListener('timeupdate', handleTimeUpdate);
        video.addEventListener(
            'loadedmetadata',
            handleLoadedMetadata
        );
        video.addEventListener('ended', handleEnded);
        video.addEventListener('play', handlePlay);
        video.addEventListener('pause', handlePause);

        return () => {
            video.removeEventListener(
                'timeupdate',
                handleTimeUpdate
            );
            video.removeEventListener(
                'loadedmetadata',
                handleLoadedMetadata
            );
            video.removeEventListener('ended', handleEnded);
            video.removeEventListener('play', handlePlay);
            video.removeEventListener('pause', handlePause);
        };
    }, [objectUrl]);

    // ============================================================
    // LECTURE / PAUSE VIDÉO
    // ============================================================

    const toggleVideo = () => {
        const video = videoRef.current;

        if (!video) {
            return;
        }

        if (video.paused) {
            void video.play();
        } else {
            video.pause();
        }
    };

    // ============================================================
    // RECHERCHE DANS LA VIDÉO
    // ============================================================

    const handleVideoSeek = (
        e: React.MouseEvent<HTMLDivElement>
    ) => {
        const video = videoRef.current;
        const bar = videoProgressBarRef.current;

        if (!video || !bar || !videoDuration) {
            return;
        }

        const rect = bar.getBoundingClientRect();

        const x = e.clientX - rect.left;

        const percentage = Math.max(
            0,
            Math.min(1, x / rect.width)
        );

        video.currentTime = percentage * videoDuration;

        setVideoCurrentTime(video.currentTime);
    };

    // ============================================================
    // FORMATAGE DU TEMPS
    // ============================================================

    const formatTime = (time: number) => {
        if (!Number.isFinite(time) || time < 0) {
            return '0:00';
        }

        const minutes = Math.floor(time / 60);

        const seconds = Math.floor(time % 60);

        return `${minutes}:${seconds
            .toString()
            .padStart(2, '0')}`;
    };

    // ============================================================
    // ÉTAT DE CHARGEMENT
    // ============================================================

    if (loadError) {
        return (
            <div className="attachment-error">
                <FileIcon />

                <span>
                    Pièce jointe indisponible
                </span>
            </div>
        );
    }

    if (!objectUrl) {
        return (
            <div className="attachment-loading">
                <span>
                    Chargement de {attachment.fileName}…
                </span>
            </div>
        );
    }

    // ============================================================
    // IMAGE
    // ============================================================

    if (isImage) {
        return (
            <div className="attachment-image-container">
                <img
                    src={objectUrl}
                    alt={attachment.fileName}
                    className="attachment-image"
                    onClick={() =>
                        setShowFullImage(true)
                    }
                />

                <div className="attachment-image-actions">
                    <button
                        type="button"
                        className="attachment-action-btn"
                        title="Agrandir"
                        onClick={() =>
                            setShowFullImage(true)
                        }
                    >
                        <ZoomInIcon />
                    </button>

                    <a
                        className="attachment-action-btn"
                        href={objectUrl}
                        download={attachment.fileName}
                        title="Télécharger"
                    >
                        <DownloadIcon />
                    </a>
                </div>

                {showFullImage && (
                    <div
                        className="attachment-fullscreen"
                        onClick={() =>
                            setShowFullImage(false)
                        }
                    >
                        <div
                            className="attachment-fullscreen-content"
                            onClick={(e) =>
                                e.stopPropagation()
                            }
                        >
                            <img
                                src={objectUrl}
                                alt={attachment.fileName}
                            />

                            <button
                                type="button"
                                className="attachment-close-btn"
                                onClick={() =>
                                    setShowFullImage(false)
                                }
                                title="Fermer"
                            >
                                ×
                            </button>

                            <a
                                className="attachment-download-btn"
                                href={objectUrl}
                                download={attachment.fileName}
                                title="Télécharger"
                            >
                                <DownloadIcon />
                            </a>
                        </div>
                    </div>
                )}
            </div>
        );
    }

    // ============================================================
    // AUDIO / MESSAGE VOCAL
    //
    // IMPORTANT :
    // Cette section est AVANT la section vidéo.
    // ============================================================

    if (isVoiceMessage) {
        const progressPercentage =
            duration > 0
                ? (currentTime / duration) * 100
                : 0;

        const waveformBars = [
            18, 28, 38, 25, 48, 62, 42, 30,
            55, 72, 45, 35, 60, 78, 52, 32,
            68, 48, 80, 58, 40, 70, 50, 30,
            64, 44, 74, 56, 36, 66, 48, 28,
            58, 42, 70, 52, 34, 62, 46, 30,
        ];

        return (
            <div className="whatsapp-audio-bubble">
                {/* Audio réel invisible */}
                <audio
                    ref={audioRef}
                    src={objectUrl}
                    preload="metadata"
                />

                {/* Avatar / microphone */}
                <div className="whatsapp-audio-avatar">
                    <div className="whatsapp-audio-mic-badge">
                        <MicIcon />
                    </div>
                </div>

                {/* Bouton lecture */}
                <button
                    type="button"
                    className="whatsapp-audio-play-btn"
                    onClick={toggleAudio}
                    title={
                        isAudioPlaying
                            ? 'Pause'
                            : 'Lecture'
                    }
                >
                    {isAudioPlaying ? (
                        <PauseIcon />
                    ) : (
                        <PlayIcon />
                    )}
                </button>

                {/* Waveform */}
                <div className="whatsapp-audio-waveform-container">
                    <div
                        className="whatsapp-audio-waveform"
                        ref={progressBarRef}
                        onClick={handleSeek}
                    >
                        {waveformBars.map(
                            (height, index) => (
                                <span
                                    key={index}
                                    className="whatsapp-audio-wave-bar"
                                    style={{
                                        height: `${height}%`,
                                    }}
                                />
                            )
                        )}

                        {/* Progression */}
                        <div
                            className="whatsapp-audio-wave-progress"
                            style={{
                                width: `${progressPercentage}%`,
                            }}
                        />
                    </div>

                    {/* Durée */}
                    <div className="whatsapp-audio-time">
                        <span>
                            {formatTime(currentTime)}
                        </span>

                        <span>
                            {formatTime(duration)}
                        </span>
                    </div>
                </div>

                {/* Téléchargement */}
                <a
                    className="attachment-action-btn whatsapp-download-icon"
                    href={objectUrl}
                    download={attachment.fileName}
                    title="Télécharger"
                >
                    <DownloadIcon />
                </a>
            </div>
        );
    }

    // ============================================================
    // VIDÉO
    //
    // Ici seulement les vraies vidéos.
    // ============================================================

    if (isVideo) {
        return (
            <div className="attachment-video-container">
                <div
                    className="attachment-video-wrapper"
                    onClick={() =>
                        setShowFullVideo(true)
                    }
                >
                    <video
                        ref={videoRef}
                        src={objectUrl}
                        preload="metadata"
                        className="attachment-video"
                    />

                    <div className="attachment-video-overlay-play">
                        <PlayIcon />
                    </div>
                </div>

                <div className="attachment-image-actions">
                    <button
                        type="button"
                        className="attachment-action-btn"
                        title="Agrandir / Plein écran"
                        onClick={() =>
                            setShowFullVideo(true)
                        }
                    >
                        <ZoomInIcon />
                    </button>

                    <a
                        className="attachment-action-btn"
                        href={objectUrl}
                        download={attachment.fileName}
                        title="Télécharger"
                    >
                        <DownloadIcon />
                    </a>
                </div>

                {showFullVideo && (
                    <div
                        className="attachment-fullscreen"
                        onClick={() =>
                            setShowFullVideo(false)
                        }
                    >
                        <div
                            className="attachment-fullscreen-content"
                            onClick={(e) =>
                                e.stopPropagation()
                            }
                        >
                            <video
                                ref={modalVideoRef}
                                src={objectUrl}
                                controls
                                autoPlay
                                className="attachment-fullscreen-video"
                            />

                            <button
                                type="button"
                                className="attachment-close-btn"
                                onClick={() =>
                                    setShowFullVideo(false)
                                }
                                title="Fermer"
                            >
                                ×
                            </button>

                            <a
                                className="attachment-download-btn"
                                href={objectUrl}
                                download={attachment.fileName}
                                title="Télécharger"
                            >
                                <DownloadIcon />
                            </a>
                        </div>
                    </div>
                )}
            </div>
        );
    }

    // ============================================================
    // FICHIER / DOCUMENT
    // ============================================================

    return (
        <div className="attachment-file-container">
            <FileIcon />

            <span className="attachment-file-name">
                {attachment.fileName}
            </span>

            <a
                className="attachment-action-btn"
                href={objectUrl}
                download={attachment.fileName}
                title="Télécharger"
            >
                <DownloadIcon />
            </a>
        </div>
    );
}
