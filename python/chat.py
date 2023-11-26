import nltk,random
from nltk import ngrams #use for DetectText
import tflearn,pickle,json #use for Chat
from nltk.stem.lancaster import LancasterStemmer #use for Chat
import os
import numpy as np
stemmer = LancasterStemmer() #use for Chat

class Chat: # pip install tflearn
    def __init__(self):
        nltk.download('punkt')
        self.rootFolder = os.path.dirname(os.path.abspath(__file__))
        self.rootFolder = os.path.join(self.rootFolder, 'models/chatbot')
        self.intents_dir = os.path.join(self.rootFolder, 'intents')  # Đường dẫn đến thư mục intents
        self.ERROR_THRESHOLD = 0.7
        self.context = {} # data structure to hold user context
        self.load_data()

        # restore our data structures
        self.data = pickle.load( open(os.path.join(self.rootFolder,"training_data" ), "rb" ) )
        self.words = self.data['words']
        self.classes = self.data['classes']
        self.train_x = self.data['train_x']
        self.train_y = self.data['train_y']
        # import intents file
        self.intents = self.load_data()
        # Define model and setup tensorboard
        self.model = self.build_model()
        self.model.load(os.path.join(self.rootFolder,'model.tflearn'))
    def clean_up_sentence(self,sentence):
        sentence_words = nltk.word_tokenize(sentence)
        sentence_words = [stemmer.stem(word.lower()) for word in sentence_words]
        return sentence_words

    # bag of words
    def bow(self,sentence, words, show_details=False):
        sentence_words = self.clean_up_sentence(sentence)
        # bag of words
        bag = [0]*len(words)  
        for s in sentence_words:
            for i,w in enumerate(words):
                if w == s: 
                    bag[i] = 1
                    if show_details:
                        print ("found in bag: %s" % w)

        return(np.array(bag))


    def load_data(self):
        data = {'intents': []}

        for folder in os.listdir(self.intents_dir):
            folder_path = os.path.join(self.intents_dir, folder)
            if os.path.isdir(folder_path):
                patterns_file = os.path.join(folder_path, 'patterns.txt')
                responses_file = os.path.join(folder_path, 'responses.txt')

                if os.path.isfile(patterns_file) and os.path.isfile(responses_file):
                    with open(patterns_file, 'r',encoding='utf-8') as f:
                        patterns = [line.strip() for line in f if line.strip()]  # Bỏ qua các dòng trống và thêm vào danh sách patterns
                    with open(responses_file, 'r', encoding='utf-8') as f:
                        responses = [line.strip() for line in f if line.strip()]  # Bỏ qua các dòng trống và thêm vào danh sách responses

                    intent = {'tag': folder, 'patterns': patterns, 'responses': responses}
                    data['intents'].append(intent)

        return data
    def build_model(self):
        # Build neural network
        net = tflearn.input_data(shape=[None, len(self.train_x[0])])
        net = tflearn.fully_connected(net, 10)
        net = tflearn.fully_connected(net, 10)
        net = tflearn.fully_connected(net, len(self.train_y[0]), activation='softmax')
        net = tflearn.regression(net, optimizer='adam', loss='categorical_crossentropy')

        # Define model and setup tensorboard
        model = tflearn.DNN(net, tensorboard_dir='tflearn_logs')
        return model
    
    
    def classify(self, sentence):
        results = self.model.predict([self.bow(sentence, self.words)])[0]
        results = [[i, r] for i, r in enumerate(results) if r > self.ERROR_THRESHOLD]
        results.sort(key=lambda x: x[1], reverse=True)
        return_list = []
        found_class = False

        for r in results:
            return_list.append((self.classes[r[0]], r[1]))
            found_class = True

        if not found_class:
            return_list.append(("unknown", 1.0))
            return return_list
        return return_list


    def response(self, sentence, userID='1', show_details=False):
        results = self.classify(sentence)
        if results:
            while results:
                for i in self.intents['intents']:
                    if 'unknown' == results[0][0]:
                        traloi = 'Tôi không hiểu yêu câu của bạn, ' + self.getRandomResponseOfATag('job')
                        return {"message": traloi, "intent": 'unknown' , "replyFor": sentence}
                    elif i['tag'] == results[0][0]:
                        if 'context_set' in i:
                            if show_details: print ('context:', i['context_set'])
                            self.context[userID] = i['context_set']
                        if not 'context_filter' in i or \
                            (userID in self.context and 'context_filter' in i and i['context_filter'] == self.context[userID]):
                            if show_details: print ('tag:', i['tag'])
                            return {"message": random.choice(i['responses']), "intent": results[0][0], "replyFor": sentence }

                results.pop(0)
    
    def getRandomResponseOfATag(self, tag):
        # Danh sách intents
    
        job_intents = [intent for intent in self.intents['intents'] if intent['tag'] == tag]

        if job_intents:
            # Lấy ngẫu nhiên một câu trả lời từ danh sách responses
            random_response = random.choice(job_intents[0]['responses'])
            return random_response