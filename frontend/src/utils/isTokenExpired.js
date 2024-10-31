import { jwtDecode } from "jwt-decode";

export function isTokenExpired (token) {

    if (!token) return true;

    try {
        const { exp } = jwtDecode(token);
        const currentTime = Math.floor(Date.now() / 1000);
        return exp < currentTime;
    } catch (error) {
        return true;
    }
}
