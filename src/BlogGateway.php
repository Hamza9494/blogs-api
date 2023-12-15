<?php
class BlogGateway
{
	private $conn;

	public function __construct(Database $database)
	{
		$this->conn = $database->connect();
	}

	public function getAll($id)
	{
		$sql = 'SELECT * FROM final_blogs WHERE user_id = :id ';
		$stmt =  $this->conn->prepare($sql);
		$stmt->bindParam(":id", $id);
		$stmt->execute();
		$blogs = [];
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$blogs[] = $row;
		}

		return $blogs;
	}

	public function create($user_id, $blog)
	{
		$sql = 'INSERT INTO final_blogs (user_id , author , title , body) VALUES ( :user_id , :author , :title , :body)';
		$stmt = $this->conn->prepare($sql);
		$stmt->bindParam(":user_id", $user_id);
		$stmt->bindParam(":author", $blog['author']);
		$stmt->bindParam(":title", $blog['title']);
		$stmt->bindParam(":body", $blog['body']);
		$stmt->execute();
		return $this->conn->lastInsertId();
	}

	public function get($id)
	{
		$sql = "SELECT * FROM final_blogs WHERE id = :id";
		$stmt = $this->conn->prepare($sql);
		$stmt->bindParam(":id", $id);
		$stmt->execute();
		$blog = $stmt->fetch(pdo::FETCH_ASSOC);
		return $blog;
	}

	public function update($new, $cur)
	{
		$sql = " UPDATE final_blogs SET author = :author , title = :title , body = :body , author = :author WHERE id = :id ";
		$stmt = $this->conn->prepare($sql);
		$stmt->bindValue(":author", $new['author'] ?? $cur['author']);
		$stmt->bindValue(":title", $new['title'] ?? $cur['title']);
		$stmt->bindValue(":body", $new['body'] ?? $cur['body']);

		$stmt->bindValue(":id", $cur['id']);
		$stmt->execute();
		$row = $stmt->rowCount();
		return $row;
	}

	public function delete($id)
	{
		$sql = "DELETE FROM final_blogs WHERE id = :id ";
		$stmt = $this->conn->prepare($sql);
		$stmt->bindParam(":id", $id);
		$stmt->execute();
		return $stmt->rowCount();
	}
}
