window.onload = function pql_start(){
    if( sessionStorage.hits ) {
        sessionStorage.hits = Number(sessionStorage.hits) +1;
    } else {
        sessionStorage.hits = 1;
    }
    document.getElementById('log').innerHTML = sessionStorage.hits;
}