import { useEffect, useState } from "react";

type User = {
    id: number;
    name: string;
    email: string;
    avatar_url?: string | null;
};

const API = "http://localhost:8000";
let deferredPrompt: any = null;
// ---- API helpers ----

async function fetchMe(): Promise<User | null> {
    const r = await fetch(`${API}/api/user`, {
        credentials: "include",
        headers: { Accept: "application/json" },
    });

    if (r.status === 401) return null;
    if (!r.ok) throw new Error(`API error: ${r.status}`);

    return (await r.json()) as User;
}
function getCookie(name: string): string | null {
    const m = document.cookie.match(new RegExp(`(^|; )${name}=([^;]*)`));
    return m ? decodeURIComponent(m[2]) : null;
}

async function logout(): Promise<void> {
    await fetch(`${API}/sanctum/csrf-cookie`, {
        credentials: "include",
        headers: { Accept: "application/json" },
    });

    const xsrf = getCookie("XSRF-TOKEN");
    if (!xsrf) throw new Error("No XSRF-TOKEN cookie found");

    const r = await fetch(`${API}/logout`, {
        method: "POST",
        credentials: "include",
        headers: {
            Accept: "application/json",
            "X-XSRF-TOKEN": xsrf,
        },
    });

    if (!r.ok && r.status !== 204) {
        throw new Error(`Logout failed: ${r.status}`);
    }
}
async function installApp() {
    if (!deferredPrompt) return;

    deferredPrompt.prompt();
    await deferredPrompt.userChoice;
    deferredPrompt = null;
}
// ---- React App ----

export default function App() {
    const [me, setMe] = useState<User | null>(null);
    const [loading, setLoading] = useState(true);
    useEffect(() => {
        const handler = (e: any) => {
            e.preventDefault();
            deferredPrompt = e;
        };

        window.addEventListener("beforeinstallprompt", handler);

        return () => {
            window.removeEventListener("beforeinstallprompt", handler);
        };
    }, []);

    useEffect(() => {
        fetchMe()
            .then(setMe)
            .catch(() => setMe(null))
            .finally(() => setLoading(false));
    }, []);

    if (loading) {
        return (
            <div style={{ maxWidth: 560, margin: "40px auto", fontFamily: "system-ui" }}>
                <p>Loading...</p>
            </div>
        );
    }

    return (
        <div style={{ maxWidth: 560, margin: "40px auto", fontFamily: "system-ui" }}>
            <h1>Birdwatch</h1>

            {!me ? (
                <>
                    <p>You are not logged in.</p>

                    <a href={`${API}/auth/google/redirect`}>
                        <button style={{ padding: "10px 14px", cursor: "pointer" }}>
                            Login with Google
                        </button>
                    </a>
                </>
            ) : (
                <div>
                    <p>✅ Logged in as:</p>

                    {me.avatar_url && (
                        <img
                            src={me.avatar_url}
                            alt="avatar"
                            style={{
                                width: 48,
                                height: 48,
                                borderRadius: "50%",
                                marginBottom: 8,
                            }}
                        />
                    )}

                    <pre
                        style={{
                            background: "#f6f8fa",
                            padding: 12,
                            borderRadius: 6,
                            overflowX: "auto",
                        }}
                    >
                        {JSON.stringify(me, null, 2)}
                    </pre>

                    <button
                        style={{
                            padding: "10px 14px",
                            cursor: "pointer",
                            marginTop: 12,
                        }}
                        onClick={async () => {
                            try {
                                await logout();
                                setMe(null);
                            } catch (e) {
                                console.error(e);
                                alert("Logout failed");
                            }
                        }}
                    >
                        Logout
                    </button>
                    {deferredPrompt && (
                        <button onClick={installApp}>
                            Install App
                        </button>
                    )}
                </div>
            )}
        </div>
    );
}