import { useEffect, useState } from "react";

type User = { id: number; name: string; email: string; avatar_url?: string | null };

const API = "http://localhost:8000";

async function fetchMe(): Promise<User | null> {
    const r = await fetch(`${API}/api/user`, {
        credentials: "include",
        headers: { Accept: "application/json" },
    });
    if (r.status === 401) return null;
    if (!r.ok) throw new Error(`API error: ${r.status}`);
    return (await r.json()) as User;
}

export default function App() {
    const [me, setMe] = useState<User | null>(null);

    useEffect(() => {
        fetchMe().then(setMe).catch(() => setMe(null));
    }, []);

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
                    <pre>{JSON.stringify(me, null, 2)}</pre>
                </div>
            )}
        </div>
    );
}