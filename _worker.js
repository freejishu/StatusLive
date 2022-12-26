let api_key = '';//你的apikey
let cache_time = 10;//缓存时间（秒）
const url = 'https://api.uptimerobot.com/v2/getMonitors';

export default {
    async fetch(request, env) {
        api_key = env.api_key ;
        cache_time = env.cache_time ? env.cache_time : 10;
        console.log(cache_time);
        const url = new URL(request.url);
        if (url.pathname.startsWith('/api')) {    
            if (request.method === 'OPTIONS') {
                return new Response(null, {
                    status: 200,
                    statusText: 'OK',
                    headers: {
                        'content-type': 'application/json;charset=UTF-8',
                        'Access-Control-Allow-Origin': '*',
                        'Access-Control-Allow-Methods': '*',
                        'Access-Control-Allow-Credentials': 'true',
                        'Access-Control-Allow-Headers': 'Content-Type',
                        'Access-Control-Expose-Headers': '*'
                    }
                })
            } else if (request.method === 'POST') {
                return await handleRequest(request,env);
            } else {
                return new Response(null, {
                    status: 405,
                    statusText: 'Method Not Allowed',
                })
            }
        }
        // Otherwise, serve the static assets.
        // Without this, the Worker will error and no assets will be served.
        return env.ASSETS.fetch(request);
    },
  }




async function gatherResponse(response) {
    const { headers } = response;
    const contentType = headers.get('content-type') || '';
    if (contentType.includes('application/json')) {
        return JSON.stringify(await response.json());
    }
    return response.text();
}

async function readRequestBody(request) {
    const { headers } = request;
    const contentType = headers.get('content-type') || '';

    if (contentType.includes('application/json')) {
        return JSON.stringify(await request.json());
    } else if (contentType.includes('application/text')) {
        return request.text();
    } else if (contentType.includes('text/html')) {
        return request.text();
    } else if (contentType.includes('form')) {
        const formData = await request.formData();
        const body = {};
        for (const entry of formData.entries()) {
        body[entry[0]] = entry[1];
        }
        return JSON.stringify(body);
    } else {
        return 'a file';
    }
}

async function handleRequest(request,env) {

    const timestamp = (Date.parse(new Date()))/1000;
    const lasttime = await env.statuslive.get("statuslive_lasttime");
    let results = '';
    let cache_tag = '';
    if (timestamp - lasttime <= cache_time) {
        results = await env.statuslive.get("statuslive_json_cache");
        cache_tag = 'Cache Hit - Time:'+lasttime;
    }else{
        const post_data = JSON.parse(await readRequestBody(request));
        post_data.api_key = api_key;

        const init = {
            headers: {
                'content-type': 'application/json;charset=UTF-8',
            },
            method: 'POST',
            body: await JSON.stringify(post_data)
        };
        const response = await fetch(url, init);
        results = await JSON.parse(await gatherResponse(response));

        for (let index = 0; index < results.monitors.length; index++) {
            results.monitors[index].url = "";
            results.monitors[index].http_username = "";
            results.monitors[index].http_password = "";
            results.monitors[index].port = "";
        }

        results = await JSON.stringify(results);
        await env.statuslive.put("statuslive_json_cache", results, {expirationTtl: (cache_time < 60 ? 60 : cache_time)})
        await env.statuslive.put("statuslive_lasttime", timestamp.toString(), {expirationTtl: (cache_time < 60 ? 60 : cache_time)})
        cache_tag = 'Cache Miss - Time:'+timestamp;
    }

    let response_init = {
        headers: {
            'content-type': 'application/json;charset=UTF-8',
            'Access-Control-Allow-Origin': '*',
            'Access-Control-Allow-Methods': '*',
            'Access-Control-Allow-Credentials': 'true',
            'Access-Control-Allow-Headers': 'Content-Type',
            'Access-Control-Expose-Headers': '*',
            'StatusLive-Cache-Tag' : cache_tag
        }
    }
    return new Response(results, response_init);
}
