class Requests {
    static form;
    static SetForm(id) {
        this.form = document.getElementById(id);
        if (!this.form) {
            throw new Error("O formulário não foi encontrado!");
        }
        return this;
    }
    
    static async Post(url) {
        try {
            // Converte FormData para JSON para melhor compatibilidade
            const formData = new FormData(this.form);
            const jsonData = {};
            
            for (let [key, value] of formData.entries()) {
                jsonData[key] = value;
            }
            
            console.log('Enviando requisição POST para:', url);
            console.log('Dados:', jsonData);
            
            const option = {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(jsonData),
                cache: 'default'
            };
            
            const response = await fetch(url, option);
            
            console.log('Status da resposta:', response.status, response.statusText);
            
            if (!response.ok) {
                const text = await response.text();
                console.error(`Erro HTTP ${response.status}:`, response.statusText);
                console.error('Resposta do servidor:', text);
                throw new Error(`Erro na requisição: ${response.status} ${response.statusText}`);
            }
            
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('Resposta não é JSON. Content-Type:', contentType);
                console.error('Conteúdo da resposta:', text);
                throw new Error('Resposta do servidor não é JSON válido');
            }
            
            const jsonResponse = await response.json();
            console.log('Resposta JSON recebida:', jsonResponse);
            return jsonResponse;
            
        } catch (error) {
            console.error('Erro em Requests.Post:', error.message);
            console.error('Stack:', error.stack);
            throw error;
        }
    }
}
export { Requests };