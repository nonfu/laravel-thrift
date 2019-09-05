namespace php App.Thrift.User

// 定义用户接口
service User {
    string getInfo(1:i32 id)
}