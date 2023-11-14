from fastapi import FastAPI, File, UploadFile, Query
from search import Search
from fastapi.middleware.cors import CORSMiddleware

import sys,os
app = FastAPI()
app_dir = os.path.dirname(os.path.abspath(__file__))

mySearch = Search()
# Thêm CORS Middleware
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # Có thể chỉ định danh sách các origin được phép, hoặc sử dụng "*" để cho phép tất cả
    allow_credentials=True,
    allow_methods=["*"],  # Cho phép tất cả các phương thức HTTP
    allow_headers=["*"],  # Cho phép tất cả các headers
)
print('==================SERVER STARTED==================')

@app.get("/")
async def root():
    return {"message": "Hello World"}

@app.post("/image")
async def predict(file: UploadFile = File(...)):
    return mySearch.search(file.file)
@app.get("/loadToAddNewImages")
async def loadToAddNewImages():
    return mySearch.loadToAddNewImages()

if __name__ == "__main__":
    import uvicorn

    # uvicorn.run(app, host="0.0.0.0", port=8000, log_level='error')
    uvicorn.run(app, host="0.0.0.0", port=8001)